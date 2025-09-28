<?php
// Test database connection with hostname
$mysql_host = 'ns96.prodns.com.br';
$mysql_database = 'terapiae_terapia';
$mysql_username = 'terapiae_terapia';
$mysql_password = 'Ha31038866##';

echo "Testando conexão com hostname (ns96.prodns.com.br)...\n\n";
echo "Host: " . $mysql_host . "\n";
echo "Database: " . $mysql_database . "\n";
echo "Username: " . $mysql_username . "\n\n";

try {
    $pdo = new PDO("mysql:host=$mysql_host;dbname=$mysql_database;charset=utf8", $mysql_username, $mysql_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Conexão com o banco de dados estabelecida com sucesso!\n\n";
    
    // Test if tables exist
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Tabelas encontradas no banco:\n";
    foreach($tables as $table) {
        echo "- " . $table . "\n";
    }
    
    if(empty($tables)) {
        echo "⚠️  Nenhuma tabela encontrada. Será necessário criar a estrutura do banco.\n";
    }
    
} catch(PDOException $e) {
    echo "❌ Erro de conexão: " . $e->getMessage() . "\n";
}
?>