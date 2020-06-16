<?php

// Error reporting
error_reporting(0);
ini_set('display_errors', '0');

// Timezone
date_default_timezone_set('Asia/Baghdad');

// Settings
$settings = [];

// Path settings
$settings['root'] = dirname(__DIR__);
$settings['temp'] = $settings['root'] . '/tmp';
$settings['public'] = $settings['root'] . '/public';

// Error Handling Middleware settings
$settings['error_handler_middleware'] = [

    // Should be set to false in production
    'display_error_details' => true,

    // Parameter is passed to the default ErrorHandler
    // View in rendered output by enabling the "displayErrorDetails" setting.
    // For the console and unit tests we also disable it
    'log_errors' => true,

    // Display error details in error log
    'log_error_details' => true,
];

// Database settings
$settings['db'] = [
    'driver' => 'mysql',
    'host' => 'localhost',
    'username' => 'root',
    'database' => 'ammen',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'flags' => [
        // Turn off persistent connections
        PDO::ATTR_PERSISTENT => false,
        // Enable exceptions
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        // Emulate prepared statements
        PDO::ATTR_EMULATE_PREPARES => true,
        // Set default fetch mode to array
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // Set character set
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci'
    ],
];




    $settings['jwt'] = [

        // The issuer name
        'issuer' => 'www.ammen.com',
    
        // Max lifetime in seconds
        'lifetime' => 2592000,
    
        // The private key
        'private_key' => '-----BEGIN RSA PRIVATE KEY-----
MIIBOgIBAAJBAMPtc86p8p5CbWDaXTCgOKhQ+UyS0BYlgfvP0GExJeaLUwrwqBkj
xUfbaxMIsENgTG07kzxhzQ5c+pR6KReeBOcCAwEAAQJBAJC/w0kxoX9ukCR371VX
acRgXm5GINnbyBZjyA2mI9wYUk7C9HT8gapGePG/O8H9aVboo3d3LvVOu0sbae8F
bjECIQD2PFrzy0xhFJ+Ns1O+Fj2d+G/DB036LaJjCkXLRUGTzQIhAMuyZZgXZfnA
lWH3pDWE84BRUYxL/TaKaQQ3NiXpX++DAiEA3YVY0cEQmrnh/Kna6cS6dDZ/3TXi
GfMaBv3D4mYQ4/UCIBsmQPI/lCDwsThoiGN1v/rHW+YmLq65Tfv42+e7rkS/Ah9X
WC0N0x90uyflvMMQ9ck+ytcmFHrGytA7Wkh49Mt8
-----END RSA PRIVATE KEY-----',
        
    
        'public_key' => '-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAMPtc86p8p5CbWDaXTCgOKhQ+UyS0BYl
gfvP0GExJeaLUwrwqBkjxUfbaxMIsENgTG07kzxhzQ5c+pR6KReeBOcCAwEAAQ==
-----END PUBLIC KEY-----'
        
    ];

    $settings['jwt2'] = [

        // The issuer name
        'issuer' => 'www.ammen.com',
    
        // Max lifetime in seconds
        'lifetime' => 86400,
    
        // The private key
        'private_key' => '-----BEGIN RSA PRIVATE KEY-----
MIIEpQIBAAKCAQEA5m+1leeKSbf8UxK/8LS//WZGsJF3bAR2zbWtppAx0540AE+M
KCFUpXlne/LLb7W9SFmo/KLxhFPXi8e+We2R6j9aTZZVj9eAiOk8eUmqDUe3pC/P
+vuSd5LeC/+YpvV7GRm4zomydT2k+46JdBFpZUwPlWewIAiFhGyP+tS3y6mxX6LH
ds/XEgpGrWG68zQ0cYmzRFbrKpwFD7HvviYuFVaLevUY4iH7Oa8R26rqzfiCKO2Z
LKi99W4RV9wWMenonze7w19vr/PScEspTz/hjmQ7lCalY/d2yoBZis4Y3uvSUqc7
dZ+/vTtDYzno5GbGNKlPql93fOE4C1IcmIOkKQIDAQABAoIBAQCHribBCQAHv+k2
EMgrNoIE9f+RX1VtqIM7REnm1OhBrdj4hd4EkvuAIyend6IJOH0m6IPznQ2P2L4F
c6rE0Hkl7/9/x5DBjSYFdy9ASvtxrlSWvAA9y3rGJRfRAMIlLE06zkZedf05wv4t
kBOm38mq90oFsnOlqN7UxgdQrYc6xqWoYCgpoLD+ePEJTXvqmC7U7fzwgiURn2pf
y4YEH7ATXaHjpVrS/Xbb22HR5gvRqWP2HlC4siwVes3UoDkqhe6z0m9WIrgFVLDV
i7LPfB9IQ0B1ZaaniqaIyQE/ookvk4O1avSlxmPq8/CvYJqkuuyWhujisOVR5G6G
ylvn7ybBAoGBAPkrVWXJM6zXhAv6t6/rhT2J/1rtaENRz7OYOZqnfSx1GV8vO6SQ
H5L1howlu0S01FqLIbB9ekHw8dbeYe29HXE5i7R72RQK3yek+OHPywQiqtKvLJZC
F0EicyF2s/x46Iqi+CCjJMynmYlJnbIQSoDPKqChxR3tde8jhxcUFyrlAoGBAOzA
6JCcHdvzZJxJATVNcqncY7jB7zqfh1Kc7WaGhx4bLCDMAhGuVC5oSVZOvFC7PwaU
1qn8ChIjYaSm3jCO6rXMyUcLHRKJKMtQPY096vYtUc41GpD3/f8gjt02Y48n6YSA
lxNH6WkqA04SzumZB3vre3M8VngBg/YQGLQIxMv1AoGBALzAlOqMZnbys/cXMHs1
oTOjDCvnWGpW2U6lbE25v1skQdoXP8lD3IdJM2mLU7eSfKMybozyIOE/ExTAVdLw
xhL1kt1gTGugaLfxgxAchyBU2q7LQK16137iB/E7qNEDDrWnCuw+aiwWcnrLp4gy
Sx4U0afppBctwX8snLNg5sP5AoGAHbdw+YW+8baxMDSxpCEefVeeZLhi8LbJY4Mz
ASVtnEfI2C0DLXj2NAT+/4hOdsup84eHEjsCgJhUTzhqtymZKEyWDwbEFWmF508h
CrN05IV3uSxNM1kNVpKdnAKRqIxX8Tu6ur7R+1M8qvYNZqDAobtC+YnfSJzYFXCb
yS04lmkCgYEA9HO0TivipVMVL0flSDpYpxXKh2JSoT6RIK13vXbL8ELL5+mUW0m/
AyNw+TnA1ZahkGNPnVfA0vLu9QbR/vBR7Xj5jvtvvph7G2CRiz91xRnuFOFibgUb
5/uZx4l34Ols7F2P4pej9NdFeB5QOvELLBns/3zLoLxGDsulH4Gx3vk=
-----END RSA PRIVATE KEY-----',
        
    
        'public_key' => '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA5m+1leeKSbf8UxK/8LS/
/WZGsJF3bAR2zbWtppAx0540AE+MKCFUpXlne/LLb7W9SFmo/KLxhFPXi8e+We2R
6j9aTZZVj9eAiOk8eUmqDUe3pC/P+vuSd5LeC/+YpvV7GRm4zomydT2k+46JdBFp
ZUwPlWewIAiFhGyP+tS3y6mxX6LHds/XEgpGrWG68zQ0cYmzRFbrKpwFD7HvviYu
FVaLevUY4iH7Oa8R26rqzfiCKO2ZLKi99W4RV9wWMenonze7w19vr/PScEspTz/h
jmQ7lCalY/d2yoBZis4Y3uvSUqc7dZ+/vTtDYzno5GbGNKlPql93fOE4C1IcmIOk
KQIDAQAB
-----END PUBLIC KEY-----'
        
    ];




return $settings;