<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Import Siswa</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
        }

        .header {
            text-align: center;
            margin-bottom: 18px;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .header p {
            margin: 4px 0 0;
            font-size: 11px;
            color: #4b5563;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #1d4ed8;
            color: #ffffff;
            font-weight: bold;
            padding: 8px;
            border: 1px solid #d1d5db;
            text-align: left;
        }

        td {
            padding: 8px;
            border: 1px solid #d1d5db;
        }

        .no {
            width: 35px;
            text-align: center;
        }

        .password {
            font-weight: bold;
            color: #dc2626;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Hasil Import Data Siswa</h2>
        <p>Dicetak pada: {{ $tanggal }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="no">No</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Password Awal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($berhasil as $index => $row)
                <tr>
                    <td class="no">{{ $index + 1 }}</td>
                    <td>{{ $row['nis'] }}</td>
                    <td>{{ $row['nama'] }}</td>
                    <td>{{ $row['kelas'] }}</td>
                    <td class="password">{{ $row['password'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>