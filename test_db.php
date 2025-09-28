<?php
// Test database connection
$mysql_host = getenv('MYSQL_HOST');
$mysql_database = getenv('MYSQL_DATABASE');
$mysql_username = getenv('MYSQL_USERNAME');
$mysql_password = getenv('MYSQL_PASSWORD');

echo "Testando conexão com o banco de dados...\n\n";
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