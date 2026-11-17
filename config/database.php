<?php
// Conexão com o Supabase (Postgres) usando o Session pooler.
// ATENÇÃO: este arquivo contém a senha do banco. Não envie para o GitHub nem para ninguém.

$host     = "aws-0-sa-east-1.pooler.supabase.com";
$port     = "5432";
$dbName   = "postgres";
$userName = "postgres.hycusvywovklivlopaeu";
$password = "Raul13725839";

try {
    $conexao = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbName;sslmode=require",
        $userName,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    // Durante os testes mostramos o erro. Em produção, troque por error_log() e uma mensagem genérica.
    exit("Erro na conexão: " . $e->getMessage());
}
