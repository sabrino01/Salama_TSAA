<?php
// Test avec les paramètres bruts de connexion
$serverName = "SLMCTRLSTK1\SQLDEVELOPER, 1434";
$connectionInfo = array(
    "Database" => "salama_tsaa",
    "UID" => "sa",
    "PWD" => "SalIfoWar"
);
$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn) {
    echo "Connexion réussie avec sqlsrv_connect";
} else {
    echo "Échec de la connexion avec sqlsrv_connect";
    print_r(sqlsrv_errors(), true);
}
?>