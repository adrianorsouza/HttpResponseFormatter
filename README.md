Laravel Response Formatter
=====================================

Small library to send a proper safe JSON `)]}',\n` format response with Laravel.

## Sample

Returning safe json preceded with `)]}',\n` formatted response:

    <?php
    use AdrianoRosa\HttpResponse\Response;
    
    $data = ['foo' => 'bar'];
    
    $response = Response::create()->safeJson($data);
    
    // )]}',\n
    // {"code":200,"status":"success","data":{"foo":"bar"}}

Returning default json formatted response:

    <?php
    use AdrianoRosa\HttpResponse\Response;
    
    $data = ['foo' => 'bar'];
    
    $response = Response::create()->toJson($data);
    
    // {"code":200,"status":"success","data":{"foo":"bar"}}

## Version 0.2.x
Compatible with Laravel 5.3

Installation

    composer require adrianorosa/http-response-formatter
    
## Version 0.1.x
Compatible with Laravel 5.2

Installation

    composer require adrianorosa/http-response-formatter:0.1.*

### License
This software is licensed under the MIT License. Please read LICENSE 
for information on the software availability and distribution.
