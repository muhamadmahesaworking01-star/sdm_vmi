@extends('layouts.app')
@section('title', 'Kalender Dunia')
@section('page_title', 'Kalender Dunia')
<div class="row g-4">
    <div class="col-lg-7"><div class="card border-0 shadow-sm"><div class="card-header bg-white"><h5 class="mb-1">Kalender</h5></div><div class="card-body"><input type="date" id="world-date" class="form-control form-control-lg"></div></div></div>
    <div class="col-lg-5"><div class="card border-0 shadow-sm"><div class="card-header bg-white"><h5 class="mb-1">Waktu Dunia</h5></div><div class="card-body" id="world-clocks"></div></div></div>
</div>
@push('scripts')
<script>
const zones=[['WIB (Jakarta)','Asia/Jakarta'],['UTC (London)','Europe/London'],['New York','America/New_York'],['Los Angeles','America/Los_Angeles'],['Tokyo','Asia/Tokyo'],['Sydney','Australia/Sydney']];
function renderClocks(){document.getElementById('world-clocks').innerHTML=zones.map(([n,z])=>`<div class="d-flex justify-content-between border-bottom py-2"><span>${n}</span><strong>${new Intl.DateTimeFormat('id-ID',{timeZone:z,dateStyle:'medium',timeStyle:'medium'}).format(new Date())}</strong></div>`).join('')}
document.getElementById('world-date').value=new Date().toLocaleDateString('en-CA');renderClocks();setInterval(renderClocks,1000);
</script>
@endpush
