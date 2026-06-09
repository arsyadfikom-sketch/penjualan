<?php

$host = "YOUR-RDS-ENDPOINT";
$db   = "db_toko";
$user = "admin";
$pass = "password";

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8",
        $user,
        $pass
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch(PDOException $e){

    die("Koneksi gagal : ".$e->getMessage());

}

if(isset($_POST['simpan'])){

    $stmt = $pdo->prepare("
        INSERT INTO stock
        (kode_barang,nama_barang,stok,harga_beli,harga_jual)
        VALUES (?,?,?,?,?)
    ");

    $stmt->execute([
        $_POST['kode_barang'],
        $_POST['nama_barang'],
        $_POST['stok'],
        $_POST['harga_beli'],
        $_POST['harga_jual']
    ]);
}

$data = $pdo->query("SELECT * FROM stock");
?>

<!DOCTYPE html>
<html>
<body>

<h2>MASTER STOCK</h2>

<form method="post">

Kode Barang :
<input type="text" name="kode_barang"><br><br>

Nama Barang :
<input type="text" name="nama_barang"><br><br>

Stock :
<input type="number" name="stok"><br><br>

Harga Beli :
<input type="number" name="harga_beli"><br><br>

Harga Jual :
<input type="number" name="harga_jual"><br><br>

<button type="submit" name="simpan">
Simpan
</button>

</form>

<hr>

<table border="1">
<tr>
<th>Kode</th>
<th>Nama</th>
<th>Stock</th>
<th>Harga Jual</th>
</tr>

<?php foreach($data as $d): ?>

<tr>
<td><?= $d['kode_barang'] ?></td>
<td><?= $d['nama_barang'] ?></td>
<td><?= $d['stok'] ?></td>
<td><?= $d['harga_jual'] ?></td>
</tr>

<?php endforeach; ?>

</table>

</body>
</html>
