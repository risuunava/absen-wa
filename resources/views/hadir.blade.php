<!DOCTYPE html>
<html>
<head>
    <title>Absensi</title>
</head>
<body>

<h2>Absensi {{ $student->name }}</h2>

<p id="status">Mengambil lokasi...</p>

<form method="POST" action="/hadir">
    @csrf

    <input type="hidden" name="student_id" value="{{ $student->id }}">
    <input type="hidden" id="lat" name="latitude">
    <input type="hidden" id="lng" name="longitude">

    <button type="submit" id="btn" disabled>HADIR</button>
</form>

<script>
const status = document.getElementById('status');
const btn = document.getElementById('btn');

if (!navigator.geolocation) {
    status.innerText = 'Browser tidak mendukung GPS';
} else {
    navigator.geolocation.getCurrentPosition(
        (pos) => {
            document.getElementById('lat').value = pos.coords.latitude;
            document.getElementById('lng').value = pos.coords.longitude;

            status.innerText = 'Lokasi siap';
            btn.disabled = false;
        },
        () => {
            status.innerText = 'Izin lokasi ditolak';
        }
    );
}
</script>

</body>
</html>
