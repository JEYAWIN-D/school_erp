<x-mail::message>
# New Admission Enquiry Received

A new enquiry has been submitted on the **{{ config('app.name') }}** portal.

<x-mail::table>
| Field | Details |
|:------|:--------|
| Enquiry No | **{{ $enquiry->enquiry_number }}** |
| Student Name | {{ $enquiry->student_name }} |
| Class Applying | {{ $enquiry->class?->name ?? '—' }} |
| Parent Name | {{ $enquiry->parent_name }} |
| Mobile | {{ $enquiry->parent_mobile }} |
| Email | {{ $enquiry->parent_email ?? '—' }} |
| Source | {{ ucfirst($enquiry->source ?? 'website') }} |
| Date & Time | {{ $enquiry->created_at->format('d M Y H:i') }} |
</x-mail::table>

Please log in to the admin panel to follow up or assign this enquiry to a counsellor.

Thanks,<br>
{{ config('app.name') }} Admissions System
</x-mail::message>
