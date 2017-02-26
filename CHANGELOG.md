v0.3.0
------
- requirements upgrade to Laravel v5.4 

v0.2.1
------
- minor refactoring
- refactor `Response::toJson()` and `Response::safeJson()`
- add `Response::jsonError()` and `Response::safeJsonError()`
- add testing append data
- add callStatic `Response::json()`, `Response::sjson()`, `Response::error()`, `Response::serror()` methods 

v0.2.0
------
- add phpunit test case
- add `Response::json()` method to allow send default JSON response format 
- requirements now the library requires PHP >= v5.6 
- requirements upgrade to Laravel v5.3
- removed deprecated method `ResponseFormatter::setAppendToResponse()` in favor of `ResponseFormatter::add()`
- removed deprecated method `ResponseFormatter::getResponseData()`
- removed deprecated method `ResponseFormatter::getResponseData()`
- removed deprecated method `ResponseFormatter::getStatusCode()`
- removed deprecated method `ResponseFormatter::getStatus()`

v0.1.0
-----
- First release
