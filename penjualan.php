<?php

$host = "YOUR-RDS-ENDPOINT";
$db   = "db_toko";
$user = "admin";
$pass = "password";

$pdo = new PDO(
    "mysql:host=$host;dbname=$db;charset=utf8",
    $user,
    $pass
);

$pdo->setAttribute(
    PDO::ATTR_ERRMODE,
    PDO::ERRMODE_EXCEPTION
);

if(isset($_POST['jual'])){

    $kode = $_POST['kode_barang'];
    $qty = $_POST['qty'];

    $get = $pdo->prepare("
        SELECT *
        FROM stock
        WHERE kode_barang = ?
    ");

    $get->execute([$kode]);

    $barang = $get->fetch();

    if($barang){

        $total =
        $barang['harga_jual']
        *
        $qty;

        $pdo->beginTransaction();

        $simpan = $pdo->prepare("
            INSERT INTO penjualan
            (
                kode_barang,
                nama_barang,
                qty,
                harga,
                total
            )
            VALUES (?,?,?,?,?)
        ");

        $simpan->execute([
            $kode,
            $barang['nama_barang'],
            $qty,
            $barang['harga_jual'],
            $total
        ]);

        $update = $pdo->prepare("
            UPDATE stock
            SET stok = stok - ?
            WHERE kode_barang = ?
        ");

        $update->execute([
            $qty,
            $kode
        ]);

        $pdo->commit();
    }
}

$data = $pdo->query("
    SELECT *
    FROM penjualan
    ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html>
<body>

<h2>PENJUALAN</h2>

<form method="post">

Kode Barang :
<input type="text" name="kode_barang"><br><br>

Qty :
<input type="number" name="qty"><br><br>

<button type="submit" name="jual">
Jual
</button>

</form>

<hr>

<table border="1">

<tr>
<th>Tanggal</th>
<th>Barang</th>
<th>Qty</th>
<th>Harga</th>
<th>Total</th>
</tr>

<?php foreach($data as $d): ?>

<tr>
<td><?= $d['tanggal'] ?></td>
<td><?= $d['nama_barang'] ?></td>
<td><?= $d['qty'] ?></td>
<td><?= $d['harga'] ?></td>
<td><?= $d['total'] ?></td>
</tr>

<?php endforeach; ?>

</table>

</body>
</html>
