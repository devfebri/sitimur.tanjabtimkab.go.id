<?php

// Debug Script untuk mengecek data berkas di server
// File: debug_berkas.php
// Letakkan di public/ dan akses via browser

echo "<h2>Debug Data Berkas - Server</h2>";

// Koneksi database (sesuaikan dengan .env server)
try {
    $host = '127.0.0.1'; // Sesuaikan dengan DB_HOST di server
    $dbname = 'sql_sitimur_tanj'; // Sesuaikan dengan DB_DATABASE di server
    $username = 'root'; // Sesuaikan dengan DB_USERNAME di server
    $password = ''; // Sesuaikan dengan DB_PASSWORD di server
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✅ Koneksi database berhasil</p>";
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ Koneksi database gagal: " . $e->getMessage() . "</p>";
    exit;
}

// Test 1: Cek tabel metode_pengadaan_berkass
echo "<h3>1. Cek Tabel metode_pengadaan_berkass</h3>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM metode_pengadaan_berkass");
    $result = $stmt->fetch();
    echo "<p>Total records di metode_pengadaan_berkass: <strong>" . $result['total'] . "</strong></p>";
    
    if($result['total'] > 0) {
        echo "<h4>Sample data:</h4>";
        $stmt = $pdo->query("SELECT id, metode_pengadaan_id, slug, nama_berkas, multiple FROM metode_pengadaan_berkass LIMIT 5");
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Metode ID</th><th>Slug</th><th>Nama Berkas</th><th>Multiple</th></tr>";
        while($row = $stmt->fetch()) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['metode_pengadaan_id']}</td>";
            echo "<td>{$row['slug']}</td>";
            echo "<td>{$row['nama_berkas']}</td>";
            echo "<td>{$row['multiple']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

// Test 2: Cek tabel pengajuans
echo "<h3>2. Cek Tabel pengajuans</h3>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM pengajuans");
    $result = $stmt->fetch();
    echo "<p>Total records di pengajuans: <strong>" . $result['total'] . "</strong></p>";
    
    if($result['total'] > 0) {
        echo "<h4>Sample data pengajuan terbaru:</h4>";
        $stmt = $pdo->query("SELECT id, nama_paket, metode_pengadaan_id, created_at FROM pengajuans ORDER BY created_at DESC LIMIT 3");
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Nama Paket</th><th>Metode ID</th><th>Created At</th></tr>";
        while($row = $stmt->fetch()) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['nama_paket']}</td>";
            echo "<td>{$row['metode_pengadaan_id']}</td>";
            echo "<td>{$row['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

// Test 3: Simulasi query dari metodePengadaanBerkas
echo "<h3>3. Simulasi Query metodePengadaanBerkas</h3>";
echo "<p>Masukkan ID pengajuan dan metode_pengadaan_id untuk test:</p>";

$test_pengajuan_id = isset($_GET['pengajuan_id']) ? $_GET['pengajuan_id'] : 1;
$test_metode_id = isset($_GET['metode_id']) ? $_GET['metode_id'] : 1;

echo "<form method='GET'>";
echo "<label>Pengajuan ID: <input type='number' name='pengajuan_id' value='$test_pengajuan_id'></label><br><br>";
echo "<label>Metode Pengadaan ID: <input type='number' name='metode_id' value='$test_metode_id'></label><br><br>";
echo "<input type='submit' value='Test Query'>";
echo "</form>";

if(isset($_GET['pengajuan_id']) || isset($_GET['metode_id'])) {
    try {
        $sql = "
            SELECT 
                mpb.id,
                mpb.slug,
                mpb.nama_berkas,
                mpb.multiple,
                COALESCE(pf.status, 0) as status,
                pf.id as pengajuan_files_id,
                pf.slug as pf_slug,
                pf.file_path
            FROM metode_pengadaan_berkass mpb
            LEFT JOIN (
                SELECT pf1.*
                FROM pengajuan_files pf1
                INNER JOIN (
                    SELECT slug, MAX(created_at) as max_created
                    FROM pengajuan_files
                    WHERE pengajuan_id = ?
                    GROUP BY slug
                ) pf2 ON pf1.slug = pf2.slug AND pf1.created_at = pf2.max_created
            ) pf ON pf.slug = mpb.slug
            WHERE mpb.metode_pengadaan_id = ?
        ";
        
        echo "<h4>Query yang dijalankan:</h4>";
        echo "<pre>$sql</pre>";
        echo "<p>Parameters: pengajuan_id = $test_pengajuan_id, metode_pengadaan_id = $test_metode_id</p>";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$test_pengajuan_id, $test_metode_id]);
        $results = $stmt->fetchAll();
        
        echo "<h4>Hasil Query (" . count($results) . " records):</h4>";
        if(count($results) > 0) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Slug</th><th>Nama Berkas</th><th>Multiple</th><th>Status</th><th>File ID</th><th>File Path</th></tr>";
            foreach($results as $row) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>{$row['slug']}</td>";
                echo "<td>{$row['nama_berkas']}</td>";
                echo "<td>{$row['multiple']}</td>";
                echo "<td>{$row['status']}</td>";
                echo "<td>{$row['pengajuan_files_id']}</td>";
                echo "<td>{$row['file_path']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: orange;'>⚠️ Tidak ada data ditemukan</p>";
            
            // Cek apakah metode_pengadaan_id ada
            $stmt_check = $pdo->prepare("SELECT COUNT(*) as total FROM metode_pengadaan_berkass WHERE metode_pengadaan_id = ?");
            $stmt_check->execute([$test_metode_id]);
            $check_result = $stmt_check->fetch();
            
            if($check_result['total'] == 0) {
                echo "<p style='color: red;'>❌ Metode pengadaan ID $test_metode_id tidak ditemukan di tabel metode_pengadaan_berkass</p>";
            } else {
                echo "<p style='color: green;'>✅ Metode pengadaan ID $test_metode_id ditemukan ({$check_result['total']} berkas)</p>";
            }
        }
        
    } catch(PDOException $e) {
        echo "<p style='color: red;'>❌ Error query: " . $e->getMessage() . "</p>";
    }
}

// Test 4: Cek environment
echo "<h3>4. Environment Info</h3>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>MySQL Version: ";
try {
    $stmt = $pdo->query("SELECT VERSION() as version");
    $result = $stmt->fetch();
    echo $result['version'];
} catch(PDOException $e) {
    echo "Error getting version";
}
echo "</p>";

echo "<h3>5. Laravel Environment Check</h3>";
echo "<p>Cek apakah file .env di server sudah benar:</p>";
if(file_exists('../.env')) {
    echo "<p style='color: green;'>✅ File .env ditemukan</p>";
    
    $env_content = file_get_contents('../.env');
    if(strpos($env_content, 'APP_ENV=production') !== false) {
        echo "<p style='color: green;'>✅ APP_ENV=production</p>";
    } else if(strpos($env_content, 'APP_ENV=local') !== false) {
        echo "<p style='color: orange;'>⚠️ APP_ENV=local (masih development)</p>";
    }
    
    if(strpos($env_content, 'APP_DEBUG=false') !== false) {
        echo "<p style='color: green;'>✅ APP_DEBUG=false</p>";
    } else if(strpos($env_content, 'APP_DEBUG=true') !== false) {
        echo "<p style='color: orange;'>⚠️ APP_DEBUG=true (masih development)</p>";
    }
} else {
    echo "<p style='color: red;'>❌ File .env tidak ditemukan</p>";
}

echo "<hr>";
echo "<p><strong>Cara penggunaan:</strong></p>";
echo "<p>1. Upload file ini ke folder public/ di server</p>";
echo "<p>2. Akses via browser: https://sitimur.tanjabtimkab.go.id/debug_berkas.php</p>";
echo "<p>3. Sesuaikan koneksi database di bagian atas file ini</p>";
echo "<p>4. Test dengan pengajuan_id dan metode_pengadaan_id yang sesuai</p>";

?>
