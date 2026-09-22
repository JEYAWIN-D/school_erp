<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Admission Enquiry</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
  <div class="w-full max-w-lg">
    <div class="text-center mb-6">
      <h1 class="text-2xl font-bold text-slate-800">Admission Enquiry</h1>
      <p class="text-slate-500 text-sm mt-1">Fill in the form below and we'll get back to you.</p>
    </div>
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg px-4 py-3 text-green-800 text-sm mb-4">{{ session('success') }}</div>
    @endif
    <div class="card space-y-4">
      <form method="POST" action="{{ route('enquiry.submit') }}" class="space-y-4" enctype="multipart/form-data">
        @csrf
        <div><label class="label">Student Name <span class="text-red-500">*</span></label>
          <input type="text" name="student_name" class="input @error('student_name') border-red-400 @enderror" required value="{{ old('student_name') }}">
          @error('student_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div><label class="label">Applying for Class <span class="text-red-500">*</span></label>
          <select name="class_id" class="select" required>
            <option value="">Select class</option>
            @foreach($classes as $c)<option value="{{ $c->id }}" @selected(old('class_id')==$c->id)>{{ $c->name }}</option>@endforeach
          </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div><label class="label">Parent/Guardian Name <span class="text-red-500">*</span></label>
            <input type="text" name="parent_name" class="input" required value="{{ old('parent_name') }}">
          </div>
          <div><label class="label">Mobile Number <span class="text-red-500">*</span></label>
            <input type="tel" name="parent_mobile" class="input" required value="{{ old('parent_mobile') }}"
                   inputmode="numeric" maxlength="10" minlength="10" pattern="[6-9][0-9]{9}"
                   oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)"
                   placeholder="10-digit mobile (e.g. 9876543210)">
            @error('parent_mobile') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
          </div>
        </div>
        <div><label class="label">Email Address</label>
          <input type="email" name="parent_email" class="input" value="{{ old('parent_email') }}">
        </div>
        <div><label class="label">Academic Year</label>
          <input type="text" class="input bg-slate-50" value="{{ $academicYear?->name ?? 'Current Year' }}" readonly>
        </div>
        <div>
          <label class="label">Supporting Documents <span class="text-slate-400 text-xs font-normal">(optional — PDF, JPG, PNG; max 2 MB each)</span></label>
          <input type="file" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png"
            class="block w-full text-sm text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-sm file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-slate-200 rounded-lg p-1">
          <p class="text-xs text-slate-400 mt-1">You may upload Birth Certificate, Aadhaar, Previous Marksheet, etc.</p>
          @error('documents.*')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="btn btn-primary w-full">Submit Enquiry</button>
      </form>
    </div>
    <p class="text-center text-xs text-slate-400 mt-4">
      Already applied? <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">Login to check status</a>
    </p>
  </div>
</body>
</html>
