<?php
echo "=== Teste de Clique no Calendário ===\n\n";

// Simular o que acontece quando clicamos em uma data
$test_date = '2025-09-30';

echo "1. Data clicada: $test_date\n";
echo "2. URL que deveria ser gerada: index.php?page=calendar&date=$test_date\n";

// Testar se essa URL funciona
$url = "http://localhost:5000/index.php?page=calendar&date=$test_date";
echo "3. Testando URL: $url\n\n";

$response = file_get_contents($url);
if ($response !== false) {
    echo "✅ URL funciona!\n";
    
    // Verificar se horários aparecem
    if (strpos($response, 'time-slot') !== false) {
        echo "✅ Horários estão sendo exibidos!\n";
        
        // Contar quantos horários
        $count = substr_count($response, 'time-slot');
        echo "Encontrados $count horários na página\n";
        
        // Verificar se seção "Escolha o Horário" aparece
        if (strpos($response, '2. Escolha o Horário') !== false) {
            echo "✅ Seção 'Escolha o Horário' aparece!\n";
        } else {
            echo "❌ Seção 'Escolha o Horário' não aparece\n";
        }
        
    } else {
        echo "❌ Horários NÃO estão sendo exibidos\n";
    }
} else {
    echo "❌ URL não funciona\n";
}

echo "\n=== Teste das Funções JavaScript ===\n";

// Testar as funções JavaScript
echo "4. Função selectDate() deve redirecionar para:\n";
echo "   index.php?page=calendar&date=$test_date\n";

echo "\n5. Função selectTime('09:00:00') deve redirecionar para:\n";
echo "   index.php?page=calendar&date=$test_date&time=09:00:00\n";

// Testar se essa URL final funciona
$final_url = "http://localhost:5000/index.php?page=calendar&date=$test_date&time=09:00:00";
echo "\n6. Testando URL final: $final_url\n";

$final_response = file_get_contents($final_url);
if ($final_response !== false) {
    echo "✅ URL final funciona!\n";
    
    // Verificar se botão continuar aparece
    if (strpos($final_response, 'Continuar para Dados Pessoais') !== false) {
        echo "✅ Botão 'Continuar para Dados Pessoais' aparece!\n";
    } else {
        echo "❌ Botão 'Continuar' não aparece\n";
    }
} else {
    echo "❌ URL final não funciona\n";
}
?>