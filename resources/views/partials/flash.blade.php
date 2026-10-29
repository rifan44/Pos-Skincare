@if(session('success'))
<div class="alert alert-success border-0 shadow-sm rounded-4">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger border-0 shadow-sm rounded-4">{{ session('error') }}</div>
@endif
@if($errors->any())
<div class="alert alert-danger border-0 shadow-sm rounded-4"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif
