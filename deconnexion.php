<?php
session_start();
require_once 'Utilisateur.php';
Utilisateur::seDeconnecter();
header('Location: index.php');
exit();