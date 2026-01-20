@extends('layouts.app')

@section('title', 'Draw Signature')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">

        <div class="card auth-card p-4">
            <h4 class="mb-3">Draw Your Signature</h4>

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="mb-3">
                <canvas id="signature-pad" width="700" height="300" style="border:2px solid #000; background:#fff;"></canvas>
            </div>

            <form method="POST" action="{{ route('signature.save') }}">
                @csrf
                <input type="hidden" name="signature" id="signature">

                <button class="btn btn-success" onclick="saveSignature()">Save</button>
                <button type="button" class="btn btn-danger ms-2" onclick="clearPad()">Clear</button>
            </form>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
let canvas = document.getElementById('signature-pad');
let ctx = canvas.getContext('2d');
let drawing = false;

// Start
canvas.addEventListener('mousedown', (e)=>{
    drawing = true;
    ctx.beginPath();
    ctx.moveTo(e.offsetX, e.offsetY);
});

canvas.addEventListener('mousemove', (e)=>{
    if (!drawing) return;
    ctx.lineTo(e.offsetX, e.offsetY);
    ctx.stroke();
});

canvas.addEventListener('mouseup', ()=> drawing = false);

// Touch support
canvas.addEventListener('touchstart', function(e){
    e.preventDefault();
    drawing = true;
    let t = e.touches[0];
    let rect = canvas.getBoundingClientRect();
    ctx.beginPath();
    ctx.moveTo(t.clientX - rect.left, t.clientY - rect.top);
});

canvas.addEventListener('touchmove', function(e){
    e.preventDefault();
    if (!drawing) return;
    let t = e.touches[0];
    let rect = canvas.getBoundingClientRect();
    ctx.lineTo(t.clientX - rect.left, t.clientY - rect.top);
    ctx.stroke();
});

canvas.addEventListener('touchend', ()=> drawing = false);

function clearPad(){
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}

function saveSignature(){
    document.getElementById('signature').value = canvas.toDataURL();
}
</script>
@endpush
