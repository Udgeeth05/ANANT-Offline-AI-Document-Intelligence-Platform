<?php
include "auth/auth_check.php";

$q = $_POST['question'];
$difficulty = $_POST['difficulty'];
$lang = $_POST['language'];

$prompt = "Answer in $lang. Difficulty level: $difficulty. Question: $q";

$data = [
  "model" => "llama3.2:latest",
  "prompt" => $prompt,
  "stream" => false
];

$ch = curl_init("http://localhost:11434/api/generate");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
  CURLOPT_POSTFIELDS => json_encode($data)
]);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
$answer = $result['response'] ?? "";

if (strlen(trim($answer)) < 10) {
    $answer = "Offline data not sufficient.\n\nSearch on web:\nhttps://www.google.com/search?q=" . urlencode($q);
}

header("Location: dashboard.php?answer=" . urlencode($answer));
