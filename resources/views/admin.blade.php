<!DOCTYPE html>
<html>
<head>
    <title>Admin Absensi</title>
</head>
<body>

<h2>Data Absensi</h2>

<table border="1">
<tr>
    <th>Nama</th>
    <th>Status</th>
    <th>Tanggal</th>
    <th>Catatan</th>
</tr>

@foreach($data as $row)
<tr>
    <td>{{ $row->student->name }}</td>
    <td>{{ $row->status }}</td>
    <td>{{ $row->created_at }}</td>
    <td>{{ $row->note }}</td>
</tr>
@endforeach

</table>

</body>
</html>
