@extends('layouts.app')

@section('title', 'Update Signature')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">

        <div class="card auth-card p-4">
            <h4 class="mb-4">Update Digital Signature</h4>

            {{-- OLD SIGNATURE --}}
            @if(isset($user) && $user->signature)
                <div class="mb-4">
                    <p class="fw-semibold">Current Signature:</p>
                    <img src="{{ asset('signatures/'.$user->signature) }}"
                         style="max-width:300px; border:1px solid #ccc; padding:10px; background:#fff;">
                </div>
            @endif

            {{-- NEW SIGNATURE --}}
            <p class="fw-semibold">Draw New Signature:</p>

            <div style="width:100%; border:2px solid #000; background:#fff;">
                <canvas id="signature-pad" style="width:100%; height:250px;"></canvas>
            </div>

            <form method="POST" action="{{ route('signature.save') }}" class="mt-3">
                @csrf
                <input type="hidden" name="signature" id="signature">

                <button class="btn btn-success" onclick="saveSignature()">Save New Signature</button>
                <button type="button" class="btn btn-danger ms-2" onclick="clearPad()">Clear</button>
            </form>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
// CANVAS SETUP
const canvas = document.getElementById("signature-pad");
const ctx = canvas.getContext("2d");
let drawing = false;
let last = { x: 0, y: 0 };

// ⭐ FIX 1 — Resize canvas correctly (VERY IMPORTANT)
function resizeCanvas() {
    const ratio = window.devicePixelRatio || 1;

    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;

    ctx.scale(ratio, ratio);
    ctx.lineWidth = 2;
    ctx.lineCap = "round";
    ctx.strokeStyle = "#000";
}

resizeCanvas();
window.addEventListener("resize", resizeCanvas);

// ⭐ FIX 2 — Exact cursor/touch position
function getPosition(e) {
    const rect = canvas.getBoundingClientRect();

    if (e.touches && e.touches.length > 0) {
        return {
            x: e.touches[0].clientX - rect.left,
            y: e.touches[0].clientY - rect.top
        };
    }

    return {
        x: e.clientX - rect.left,
        y: e.clientY - rect.top
    };
}

// Start drawing
function startDrawing(e) {
    drawing = true;
    last = getPosition(e);
}

// Draw
function draw(e) {
    if (!drawing) return;

    const pos = getPosition(e);

    ctx.beginPath();
    ctx.moveTo(last.x, last.y);
    ctx.lineTo(pos.x, pos.y);
    ctx.stroke();

    last = pos;
}

// Stop drawing
function stopDrawing() {
    drawing = false;
}

// MOUSE EVENTS
canvas.addEventListener("mousedown", startDrawing);
canvas.addEventListener("mousemove", draw);
canvas.addEventListener("mouseup", stopDrawing);
canvas.addEventListener("mouseleave", stopDrawing);

// TOUCH EVENTS
canvas.addEventListener("touchstart", function(e) {
    e.preventDefault();
    startDrawing(e);
});
canvas.addEventListener("touchmove", function(e) {
    e.preventDefault();
    draw(e);
});
canvas.addEventListener("touchend", stopDrawing);

// CLEAR PAD
function clearPad() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}

// SAVE SIGNATURE
function saveSignature() {
    document.getElementById('signature').value = canvas.toDataURL('image/png');
}
</script>
@endpush
