<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\FeePayment;
use App\Models\FeePaymentSplit;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FeePaymentModuleTest extends TestCase
{
    protected User $admin;
    protected Student $student;
    protected AcademicYear $year;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::first() ?? User::factory()->create();
        $this->year = AcademicYear::current() ?? AcademicYear::first();

        // Create or find a test student with standard 2-term admission fee
        $this->student = Student::where('admission_no', 'TEST-ADM-999')->first() ?? Student::create([
            'admission_no'             => 'TEST-ADM-999',
            'first_name'               => 'TestStudent',
            'last_name'                => 'Auto',
            'gender'                   => 'male',
            'status'                   => 'active',
            'payment_terms'            => '2_terms',
            'total_admission_fee'      => 69800.00,
            'admission_paid_amount'    => 34900.00,
            'admission_pending_amount' => 34900.00,
            'payment_status'           => 'partially_paid',
            'admission_fee_terms'      => [
                [
                    'term_number'  => 1,
                    'name'         => 'Term 1',
                    'amount'       => 34900,
                    'paid'         => 34900,
                    'pending'      => 0,
                    'due_date'     => '2026-09-02',
                    'status'       => 'paid',
                    'payment_mode' => 'UPI',
                    'payment_date' => '2026-09-02',
                ],
                [
                    'term_number'  => 2,
                    'name'         => 'Term 2',
                    'amount'       => 34900,
                    'paid'         => 0,
                    'pending'      => 34900,
                    'due_date'     => '2026-12-01',
                    'status'       => 'pending',
                    'payment_mode' => null,
                    'payment_date' => null,
                ],
            ],
        ]);
    }

    private function resetStudentTerms(): void
    {
        $this->student->update([
            'admission_paid_amount'    => 34900.00,
            'admission_pending_amount' => 34900.00,
            'payment_status'           => 'partially_paid',
            'admission_fee_terms'      => [
                [
                    'term_number'  => 1,
                    'name'         => 'Term 1',
                    'amount'       => 34900,
                    'paid'         => 34900,
                    'pending'      => 0,
                    'due_date'     => '2026-09-02',
                    'status'       => 'paid',
                    'payment_mode' => 'UPI',
                    'payment_date' => '2026-09-02',
                ],
                [
                    'term_number'  => 2,
                    'name'         => 'Term 2',
                    'amount'       => 34900,
                    'paid'         => 0,
                    'pending'      => 34900,
                    'due_date'     => '2026-12-01',
                    'status'       => 'pending',
                    'payment_mode' => null,
                    'payment_date' => null,
                ],
            ],
        ]);
    }

    /** Test 12: Term Display */
    public function test_fee_collect_screen_displays_term_and_due(): void
    {
        $this->resetStudentTerms();
        $response = $this->actingAs($this->admin)
            ->get(route('fees.collect', ['student_id' => $this->student->id]));

        $response->assertStatus(200);
        $response->assertSee('FeePayment');
        $response->assertSee('Term 2 — ₹34,900.00 due');
        $response->assertDontSee('Advance Payment');
        $response->assertDontSee('name="late_fee"', false);
    }

    /** Test 6: Invalid Split Total rejected */
    public function test_invalid_split_total_is_rejected(): void
    {
        $this->resetStudentTerms();
        $response = $this->actingAs($this->admin)->post(route('fees.collect.save'), [
            'student_id'   => $this->student->id,
            'fee_item_id'  => 'term_2',
            'amount'       => 34900,
            'discount'     => 0,
            'payment_date' => today()->toDateString(),
            'payment_type' => 'split',
            'splits'       => [
                ['payment_mode' => 'cash', 'amount' => 20000],
                ['payment_mode' => 'upi',  'amount' => 10000], // sum = 30000 != 34900
            ],
        ]);

        $response->assertSessionHasErrors('splits');
    }

    /** Test 7: Zero payment rejected */
    public function test_zero_payment_is_rejected(): void
    {
        $this->resetStudentTerms();
        $response = $this->actingAs($this->admin)->post(route('fees.collect.save'), [
            'student_id'   => $this->student->id,
            'fee_item_id'  => 'term_2',
            'amount'       => 0,
            'discount'     => 0,
            'payment_date' => today()->toDateString(),
            'payment_type' => 'single',
            'payment_mode' => 'cash',
        ]);

        $response->assertSessionHasErrors('amount');
    }

    /** Test 8: Negative payment rejected */
    public function test_negative_payment_is_rejected(): void
    {
        $this->resetStudentTerms();
        $response = $this->actingAs($this->admin)->post(route('fees.collect.save'), [
            'student_id'   => $this->student->id,
            'fee_item_id'  => 'term_2',
            'amount'       => -100,
            'discount'     => 0,
            'payment_date' => today()->toDateString(),
            'payment_type' => 'single',
            'payment_mode' => 'cash',
        ]);

        $response->assertSessionHasErrors('amount');
    }

    /** Test 9: Overpayment rejected */
    public function test_overpayment_is_rejected(): void
    {
        $this->resetStudentTerms();
        $response = $this->actingAs($this->admin)->post(route('fees.collect.save'), [
            'student_id'   => $this->student->id,
            'fee_item_id'  => 'term_2',
            'amount'       => 40000, // exceeds due 34900
            'discount'     => 0,
            'payment_date' => today()->toDateString(),
            'payment_type' => 'single',
            'payment_mode' => 'cash',
        ]);

        $response->assertSessionHasErrors('amount');
    }

    /** Test 3: Split Cash + UPI Payment */
    public function test_split_cash_and_upi_payment_saves_correctly(): void
    {
        $this->resetStudentTerms();
        $response = $this->actingAs($this->admin)->post(route('fees.collect.save'), [
            'student_id'   => $this->student->id,
            'fee_item_id'  => 'term_2',
            'amount'       => 34900,
            'discount'     => 0,
            'payment_date' => today()->toDateString(),
            'payment_type' => 'split',
            'splits'       => [
                ['payment_mode' => 'cash', 'amount' => 20000, 'transaction_id' => 'CASH-REC'],
                ['payment_mode' => 'upi',  'amount' => 14900, 'transaction_id' => 'UPI-REC-123'],
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check payment record
        $payment = FeePayment::where('student_id', $this->student->id)->latest('id')->first();
        $this->assertNotNull($payment);
        $this->assertEquals(34900.00, (float)$payment->total_paid);
        $this->assertEquals('split', $payment->payment_mode);
        $this->assertEquals(2, $payment->term_number);

        // Check splits table
        $this->assertEquals(2, $payment->splits()->count());
        $cashSplit = $payment->splits()->where('payment_mode', 'cash')->first();
        $this->assertEquals(20000.00, (float)$cashSplit->amount);
        $upiSplit = $payment->splits()->where('payment_mode', 'upi')->first();
        $this->assertEquals(14900.00, (float)$upiSplit->amount);

        // Check student balance updated
        $this->student->refresh();
        $this->assertEquals(0.00, (float)$this->student->admission_pending_amount);
        $this->assertEquals(69800.00, (float)$this->student->admission_paid_amount);
        $this->assertEquals('paid', $this->student->payment_status);

        // Check term 2 is paid
        $terms = $this->student->admission_fee_terms;
        $this->assertEquals(0, $terms[1]['pending']);
        $this->assertEquals('paid', $terms[1]['status']);
    }

    /** Test 4: Partial Payment */
    public function test_partial_payment_updates_remaining_balance(): void
    {
        $this->resetStudentTerms();
        $response = $this->actingAs($this->admin)->post(route('fees.collect.save'), [
            'student_id'   => $this->student->id,
            'fee_item_id'  => 'term_2',
            'amount'       => 10000,
            'discount'     => 0,
            'payment_date' => today()->toDateString(),
            'payment_type' => 'single',
            'payment_mode' => 'cash',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->student->refresh();
        $this->assertEquals(24900.00, (float)$this->student->admission_pending_amount);
        $this->assertEquals(44900.00, (float)$this->student->admission_paid_amount);
        $this->assertEquals('partially_paid', $this->student->payment_status);

        $terms = $this->student->admission_fee_terms;
        $this->assertEquals(10000, $terms[1]['paid']);
        $this->assertEquals(24900, $terms[1]['pending']);
        $this->assertEquals('partially_paid', $terms[1]['status']);
    }
}
