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

if(isset($_POST['simpan'])){

    $kode = $_POST['kode_barang'];
    $qty = $_POST['qty'];
    $jenis = $_POST['jenis'];

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO transaksi
        (kode_barang,jenis,qty,keterangan)
        VALUES (?,?,?,?)
    ");

    $stmt->execute([
        $kode,
        $jenis,
        $qty,
        $_POST['keterangan']
    ]);

    if($jenis == "MASUK"){

        $up = $pdo->prepare("
            UPDATE stock
            SET stok = stok + ?
            WHERE kode_barang = ?
        ");

        $up->execute([$qty,$kode]);

    }else{

        $up = $pdo->prepare("
            UPDATE stock
            SET stok = stok - ?
            WHERE kode_barang = ?
        ");

        $up->execute([$qty,$kode]);
    }

    $pdo->commit();
}
?>

<!DOCTYPE html>
<html>
<body>

<h2>TRANSAKSI STOCK</h2>

<form method="post">

Kode Barang :
<input type="text" name="kode_barang"><br><br>

Jenis :
<select name="jenis">
<option value="MASUK">MASUK</option>
<option value="KELUAR">KELUAR</option>
</select>

<br><br>

Qty :
<input type="number" name="qty"><br><br>

Keterangan :
<input type="text" name="keterangan"><br><br>

<button type="submit" name="simpan">
Proses
</button>

</form>

</body>
</html>
