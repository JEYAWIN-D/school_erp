<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookIssue extends Model
{
    protected $fillable = [
        'book_id', 'student_id', 'employee_id', 'issued_by', 'issue_date', 'due_date', 'return_date',
        'status', 'fine_amount', 'fine_paid', 'renew_count',
        'fine_waived', 'waiver_amount', 'waiver_reason', 'waived_by', 'waived_at',
        'remarks', 'returned_to',
    ];

    protected $casts = [
        'issue_date'  => 'date',
        'due_date'    => 'date',
        'return_date' => 'date',
        'waived_at'   => 'datetime',
        'fine_waived' => 'boolean',
        'fine_amount' => 'decimal:2',
        'waiver_amount' => 'decimal:2',
    ];

    public function book(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function borrowerName(): string
    {
        if ($this->employee_id && $this->employee) {
            return $this->employee->first_name . ' ' . $this->employee->last_name;
        }
        return $this->student?->full_name ?? 'Unknown';
    }

    public function getDaysOverdueAttribute(): int
    {
        if ($this->status !== 'issued' || !$this->due_date) return 0;
        return max(0, now()->startOfDay()->diffInDays($this->due_date->startOfDay(), false) * -1);
    }

    public function issuedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function waivedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'waived_by');
    }
}
