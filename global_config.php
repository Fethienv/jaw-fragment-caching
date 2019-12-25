<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$unique_sufix         = "azerty";
$jaw_fragments_apikey = "54309511-0371-4db2-99b3-5e2967302af3";
$FRAGMENT_DIR         = 'wp-content/cache/';
$wordpress_root       = ''; // empty => in root, folder name if it not in root


//Security options
$allow_delete = true; // Set to false to disable delete button and delete POST request.
$allow_upload = false; // Set to true to allow upload files
$allow_create_folder = false; // Set to false to disable folder creation
$allow_direct_link = true; // Set to false to only allow downloads and not direct link
$allow_show_folders = true; // Set to false to hide all subdirectories

$disallowed_patterns = ['*.php'];  // must be an array.  Matching files not allowed to be uploaded
$hidden_patterns = [];//['*.php','.*']; // Matching files hidden in directory index

$PASSWORD = '';  // Set the password, to access the file manager... (optional)