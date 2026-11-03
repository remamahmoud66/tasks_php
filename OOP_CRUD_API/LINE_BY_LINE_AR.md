# الشرح التفصيلي سطرًا سطرًا — مشروع OOP CRUD API

هذا الشرح يتبع الملفات الموجودة في المشروع. اقرئيه بالترتيب، ولا تحاولي حفظ الكود كله دفعة واحدة.

---

# الجزء 1: `db.php` — الاتصال والدوال التي تتعامل مع قاعدة البيانات

```php
<?php
declare(strict_types=1);
```
- `<?php`: يبدأ كود PHP.
- `declare(strict_types=1);`: يجعل PHP أكثر صرامة في أنواع البيانات عند تمرير القيم إلى الدوال التي تعرّف أنواعًا محددة.

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'register_task');
define('DB_PORT', '3307');
```
- `define()` يعرّف ثابتًا لا تتغير قيمته أثناء تشغيل البرنامج.
- `DB_HOST`: عنوان خادم قاعدة البيانات، وفي جهازك المحلي غالبًا `localhost`.
- `DB_USER` و`DB_PASS`: اسم مستخدم MySQL وكلمة مروره.
- `DB_NAME`: اسم قاعدة البيانات.
- `DB_PORT`: منفذ MySQL؛ يجب أن يطابق إعداد XAMPP عندك.

```php
class DB_con
{
    private PDO $connect;
```
- `class DB_con`: تعريف كلاس يجمع وظائف قاعدة البيانات.
- `private`: لا يمكن الوصول إلى الخاصية مباشرة من خارج الكلاس.
- `PDO $connect`: خاصية ستحتفظ باتصال PDO.

```php
public function __construct()
{
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
```
- `__construct()` تعمل تلقائيًا عند إنشاء كائن من الكلاس.
- `$dsn` نص يصف نوع قاعدة البيانات ومكانها واسمها وترميزها.
- النقطة `.` في PHP تجمع النصوص معًا.
- `utf8mb4` يساعد على تخزين النصوص متعددة اللغات، ومنها العربية.

```php
$this->connect = new PDO($dsn, DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);
```
- `new PDO(...)` ينشئ اتصالًا بقاعدة البيانات.
- `$this->connect` تعني خاصية الاتصال التابعة للكائن الحالي.
- `ERRMODE_EXCEPTION`: يجعل أخطاء PDO تُرمى كاستثناءات يمكن التقاطها.
- `FETCH_ASSOC`: يعيد الصفوف على شكل associative arrays، مثل `$row['name']`.
- `EMULATE_PREPARES => false`: يطلب من PDO استخدام prepared statements الحقيقية عندما يدعمها محرك قاعدة البيانات.

```php
public function getConnection(): PDO
{
    return $this->connect;
}
```
- دالة تعيد اتصال PDO نفسه عند الحاجة.
- `: PDO` يوضح نوع القيمة المتوقعة من الدالة.

## دوال المستخدمين

```php
public function fetchUsers(): array
{
    return $this->connect->query('SELECT id, name, email FROM users ORDER BY id DESC')->fetchAll();
}
```
- `fetchUsers()` تجلب كل المستخدمين.
- الاستعلام لا يطلب عمود `password`، لذلك لا نرسله للواجهة.
- `ORDER BY id DESC` يعرض الأحدث بحسب رقم ID أولًا.
- `query()` تنفذ الاستعلام.
- `fetchAll()` تجلب كل الصفوف.
- `: array` يعني أن الدالة تعيد مصفوفة.

```php
public function fetchUser(int $id): array|false
```
- تجلب مستخدمًا واحدًا بحسب ID.
- `int $id` يعني أن ID عدد صحيح.
- `array|false` يعني أنها تعيد بيانات المستخدم كمصفوفة، أو `false` إذا لم يوجد.

داخلها:
- `prepare(...)` يجهز استعلامًا يحتوي على علامة `?`.
- `execute([$id])` يضع قيمة ID في مكان علامة الاستفهام وينفذ الاستعلام.
- `fetch()` تجلب صفًا واحدًا.

```php
public function createUser(string $name, string $email, string $password): bool
```
- تستقبل الاسم والبريد وكلمة المرور.
- `password_hash(...)` يحوّل كلمة المرور إلى hash قبل تخزينها؛ لا نخزّن كلمة المرور الجديدة كنص مكشوف.
- `prepare()` و`execute()` يفصلان الاستعلام عن القيم المدخلة.
- `bool` يعني أن الدالة تعيد نجاحًا أو فشلًا على شكل true/false.

```php
public function updateUser(int $id, string $name, string $email, ?string $password = null): bool
```
- تحدث بيانات المستخدم.
- `?string` يعني أن كلمة المرور قد تكون نصًا أو `null`.
- إذا أُرسلت كلمة مرور جديدة، تُخزّن بعد `password_hash()`.
- إذا تُركت فارغة، نحدّث الاسم والبريد فقط ولا نغيّر كلمة المرور.

```php
public function deleteUser(int $id): bool
```
- تحذف المستخدم الذي يطابق ID.
- `WHERE id = ?` مهم حتى لا نحذف كل المستخدمين.
- `execute([$id])` يربط ID بالاستعلام.

## دوال التصنيفات
- `fetchCategories()`: تجلب كل التصنيفات.
- `fetchCategory($id)`: تجلب تصنيفًا واحدًا.
- `createCategory($name, $description)`: تضيف تصنيفًا.
- `updateCategory($id, ...)`: تعدل تصنيفًا.
- `deleteCategory($id)`: تحذف تصنيفًا.
كلها تستخدم `prepare()` و`execute()` عند إدخال قيم من المستخدم، وتعيد البيانات أو نتيجة التنفيذ.

## دوال المنتجات

```php
public function fetchProducts(): array
```
- تجلب المنتجات.
- الاستعلام يستخدم `LEFT JOIN` لربط `products` بجدول `categories`.
- `categories.name AS category_name` يضيف اسم التصنيف إلى بيانات كل منتج، بدل أن نعرض رقم التصنيف فقط.
- `LEFT JOIN` يسمح بظهور المنتج حتى لو لم يوجد تصنيف مطابق، إن كانت قيود قاعدة البيانات تسمح بذلك.

`fetchProduct($id)` تجلب منتجًا واحدًا.

`createProduct(...)` تضيف منتجًا باستخدام الاسم والوصف والسعر والكمية ورقم التصنيف ومسار الصورة.

`updateProduct(...)` تحدّث بيانات المنتج بحسب ID.

`deleteProduct($id)` تحذف المنتج بحسب ID.

**مهم:** هذا الملف لا يعرض HTML ولا يعيد JSON بنفسه. هو طبقة التعامل مع قاعدة البيانات فقط. ملف الـ API يستدعي دواله.

---

# الجزء 2: `api/common.php` — الأدوات المشتركة لكل API

```php
header('Content-Type: application/json; charset=utf-8');
```
يخبر المتصفح أو البرنامج الذي أرسل الطلب أن الاستجابة JSON بترميز UTF-8.

```php
require_once __DIR__ . '/../db.php';
```
- `__DIR__` هو مسار المجلد الذي يوجد فيه الملف الحالي، أي مجلد `api`.
- `../db.php` يعني ارجع مجلدًا واحدًا ثم افتح `db.php`.
- `require_once` يضمّن الملف مرة واحدة فقط.

```php
function respond(array $data, int $status = 200): never
```
دالة مشتركة لإنهاء الطلب وإرسال JSON:
- `$data`: البيانات التي سنرسلها.
- `$status = 200`: رمز HTTP الافتراضي للنجاح.
- `never`: توضيح أن الدالة لن تعود إلى الكود الذي استدعاها، لأنها تنهي التنفيذ.

```php
http_response_code($status);
echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;
```
- `http_response_code()` يحدد رمز حالة HTTP.
- `json_encode()` يحوّل مصفوفة PHP إلى نص JSON.
- `JSON_UNESCAPED_UNICODE` يبقي الحروف العربية مقروءة بدل تحويلها إلى ترميزات طويلة.
- `JSON_UNESCAPED_SLASHES` يمنع تحويل الشرطات المائلة إلى صيغة escaped غير ضرورية.
- `exit` ينهي تنفيذ الملف حتى لا تُرسل استجابة إضافية بالخطأ.

مثال:
```php
respond(['success' => true, 'data' => $products]);
```
قد ينتج JSON مثل:
```json
{"success":true,"data":[{"id":1,"name":"Coffee Box"}]}
```

```php
function requestData(): array
```
تحدد كيف نقرأ البيانات التي أرسلها المستخدم:
- إذا كان نوع المحتوى `application/json`، نقرأ جسم الطلب من `php://input`.
- `json_decode(..., true)` يحوّل JSON إلى associative array في PHP.
- إذا لم يكن JSON، نعيد `$_POST`. وهذا مهم عند رفع الصور باستخدام `multipart/form-data`.

```php
function positiveId(mixed $value): int
```
- تستقبل قيمة ID قد تكون نصًا أو رقمًا أو null.
- `FILTER_VALIDATE_INT` يتحقق أنها عدد صحيح.
- لا تقبل إلا رقمًا أكبر من صفر؛ وإلا تعيد `0` كإشارة إلى أن ID غير صالح.

## رفع الصور
`uploadProductImage()`:
1. تتحقق أن الملف أُرسل دون خطأ.
2. تتحقق من أن حجمه لا يتجاوز 2MB.
3. تستخدم `finfo` لفحص نوع الملف الفعلي، لا الاسم وحده.
4. تسمح بـ JPEG وPNG وGIF وWEBP فقط.
5. تنشئ اسمًا عشوائيًا لتقليل احتمال استبدال صورة أخرى بالخطأ.
6. تنقل الملف إلى مجلد `uploads`.
7. تعيد المسار النسبي الذي نخزنه في قاعدة البيانات، أو `false` إذا فشل الرفع، أو `null` إذا لم يرسل المستخدم صورة.

`removeProductImage()`:
- لا تحاول حذف ملف إلا إذا كان مساره يبدأ بـ `uploads/`.
- تتحقق من وجود الملف، ثم تحذفه بـ `unlink()`.

---

# الجزء 3: `api/products.php` — API المنتجات

```php
require_once __DIR__ . '/common.php';
```
يستورد أدوات JSON والاتصال بالداتا بيس وقراءة الطلبات.

```php
$db = new DB_con();
$method = $_SERVER['REQUEST_METHOD'];
```
- ينشئ اتصال قاعدة البيانات عبر الكلاس.
- يقرأ نوع طلب HTTP، مثل GET أو POST أو DELETE.

## GET — قراءة البيانات

```php
if ($method === 'GET')
```
يعني أن العميل يطلب قراءة بيانات.

```php
$id = positiveId($_GET['id'] ?? null);
```
- يقرأ `id` من عنوان الطلب، مثل `api/products.php?id=3`.
- `??` تعني: إذا كانت القيمة غير موجودة أو null، استخدم القيمة البديلة `null`.

إذا كان ID صالحًا:
- `fetchProduct($id)` يجلب منتجًا واحدًا.
- إذا لم يوجد، نعيد 404.
- إذا وجد، نعيد بياناته داخل JSON.

إذا لم يوجد ID:
- `fetchProducts()` تجلب كل المنتجات.
- `respond()` ترسلهم كـ JSON.

## POST — إضافة أو تعديل منتج

هذا المشروع يستخدم POST للمنتج لأن رفع الصورة يحتاج `multipart/form-data`، وPHP تتعامل معه بسهولة عبر `$_POST` و`$_FILES`.

```php
$data = requestData();
```
يقرأ حقول النموذج.

ثم:
- `$id`: إذا وُجد، نعدّل المنتج؛ وإذا لم يوجد، نضيف منتجًا.
- `$name`, `$description`, `$price`, `$quantity`, `$categoryId`: قراءة القيم.
- `trim()` يزيل المسافات من بداية النص ونهايته.
- `filter_var()` يتحقق من السعر والكمية.
- إذا كانت القيم غير صالحة، نرسل حالة `422` ورسالة مفهومة.

```php
$oldProduct = $id > 0 ? $db->fetchProduct($id) : false;
```
هذا تعبير شرطي مختصر:
- إذا كان ID أكبر من صفر، اجلب المنتج القديم.
- وإلا اجعل القيمة `false`.

```php
$newImage = uploadProductImage($_FILES['image'] ?? null);
```
يحاول رفع صورة جديدة إن أُرسلت.

```php
$image = $newImage ?? ($oldProduct['image'] ?? null);
```
- إذا وُجدت صورة جديدة، استخدمها.
- إذا لم توجد صورة جديدة وكان تعديلًا، احتفظ بمسار الصورة القديمة.
- إذا كانت إضافة ولم توجد صورة، تكون القيمة null.

بعد ذلك:
- إذا كان ID موجودًا، نحدّث المنتج ونحذف الصورة القديمة فقط إذا نجح رفع صورة بديلة.
- إذا لم يوجد ID، نضيف منتجًا جديدًا.
- نعيد رسالة JSON توضح النجاح.

## DELETE — حذف منتج

```php
parse_str(file_get_contents('php://input'), $input);
```
يقرأ بيانات جسم طلب DELETE إذا أُرسلت كبيانات نموذجية.

```php
$id = positiveId($_GET['id'] ?? $input['id'] ?? null);
```
يحاول أخذ ID من الرابط أولًا، أو من جسم الطلب.

بعدها:
1. يتحقق من صحة ID.
2. يجلب المنتج؛ إذا لم يوجد يعيد 404.
3. يحذف سجل المنتج من الداتا بيس.
4. يحذف ملف صورته من `uploads`.
5. يرسل استجابة نجاح.

```php
catch (Throwable $e)
```
إذا حدث خطأ غير متوقع، يسجله في سجل أخطاء PHP ويرسل رسالة عامة للعميل بدل كشف تفاصيل قاعدة البيانات للمستخدم.

---

# الجزء 4: `api/categories.php` و`api/users.php`

كلا الملفين يتبعان الفكرة نفسها:
- `GET`: قراءة كل السجلات أو سجل واحد.
- `POST`: إنشاء سجل.
- `PUT`: تعديل سجل.
- `DELETE`: حذف سجل.
- `respond()`: إرسال النتيجة بصيغة JSON.

## الفرق في `categories.php`
- يتعامل مع `name` و`description`.
- إذا حاول المستخدم حذف تصنيف ما زالت منتجات مرتبطة به، قد تمنع قاعدة البيانات الحذف؛ عندها نعيد `409 Conflict` ورسالة تشرح السبب.

## الفرق في `users.php`
- يتعامل مع الاسم والبريد وكلمة المرور.
- يتحقق من صحة البريد عبر `FILTER_VALIDATE_EMAIL`.
- يطلب كلمة مرور لا تقل عن 8 أحرف عند إنشاء مستخدم.
- عند التعديل، كلمة المرور الفارغة تعني عدم تغيير كلمة المرور.
- لا يعيد كلمة المرور ضمن بيانات GET.

ملاحظة: هذا مشروع CRUD تعليمي، ولا يحتوي على تسجيل دخول أو صلاحيات. لا تنشريه على الإنترنت كما هو.

---

# الجزء 5: `assets/js/api.js` — حلقة الوصل من جهة الواجهة

```javascript
const API_BASE = 'api';
```
نحفظ اسم مجلد الـ API بمكان واحد. لذلك عنوان `products.php` يصبح `api/products.php`.

```javascript
async function apiRequest(endpoint, options = {})
```
- دالة JavaScript مشتركة لإرسال طلب API.
- `async` يسمح باستخدام `await` داخل الدالة.
- `endpoint` هو الملف المطلوب، مثل `products.php`.
- `options` يحتوي طريقة الطلب أو جسم البيانات أو headers.

```javascript
const response = await fetch(`${API_BASE}/${endpoint}`, options);
```
- `fetch()` يرسل طلب HTTP من المتصفح إلى PHP.
- `await` ينتظر وصول الاستجابة.
- `${...}` طريقة إدخال قيمة متغير داخل template string.

```javascript
const result = await response.json();
```
يقرأ جسم الاستجابة ويحوّل JSON إلى كائن JavaScript.

```javascript
if (!response.ok || result.success === false)
```
إذا كان رمز HTTP ليس نجاحًا، أو كانت الاستجابة تقول إن العملية فشلت، نرمي خطأ كي تعرض الواجهة الرسالة.

```javascript
return result;
```
يعيد البيانات للملف الذي استدعى الدالة.

## `showMessage()`
تضع رسالة في عنصر HTML، وتضيف class لتنسيق النجاح أو الخطأ.

## `escapeHTML()`
تحوّل رموزًا خاصة إلى كيانات HTML قبل إدراج بيانات المستخدم في نص HTML. هذا يقلل خطر تفسير النص المدخل كوسوم HTML عند عرضه.

---

# الجزء 6: `assets/js/products.js` — كيف تعمل صفحة المنتجات؟

1. `getElementById()` يمسك عناصر HTML التي سنحدّثها.
2. `loadCategoriesIntoSelect()` تطلب التصنيفات من API وتضعها في القائمة المنسدلة.
3. `loadProducts()` تطلب المنتجات من `api/products.php`.
4. `result.data` هي مصفوفة المنتجات الموجودة داخل استجابة JSON.
5. `forEach()` تمر على كل منتج.
6. `createElement('tr')` تنشئ صف جدول جديدًا.
7. `innerHTML` تضع خلايا الصف وأزرار Edit وDelete.
8. `appendChild()` تضيف الصف إلى الجدول.

## عند إرسال النموذج
```javascript
productForm.addEventListener('submit', async event => {
  event.preventDefault();
```
- ينتظر حدث إرسال النموذج.
- `preventDefault()` يمنع المتصفح من إعادة تحميل الصفحة بالطريقة التقليدية.

```javascript
const formData = new FormData();
```
ينشئ حاوية بيانات يمكنها حمل النصوص والملفات.

بعدها نضيف الاسم والوصف والسعر والكمية والتصنيف والـ ID إن كان تعديلًا، ثم الصورة إذا اختار المستخدم صورة.

```javascript
apiRequest('products.php', { method: 'POST', body: formData })
```
يرسل البيانات إلى API. لا نضع `Content-Type` يدويًا مع `FormData`؛ المتصفح يضعه مع boundary المناسب للملف.

عند النجاح:
- نعرض الرسالة.
- نفرغ النموذج.
- نعيد تحميل المنتجات.

عند الفشل:
- نعرض رسالة الخطأ ولا نترك الصفحة تنهار.

## عند الضغط على Edit
- نقرأ `data-edit` من الزر.
- نطلب المنتج المحدد عبر `products.php?id=...`.
- نضع بيانات المنتج في النموذج.
- نملأ قائمة التصنيفات مع اختيار تصنيف المنتج الحالي.
- عندما يرسل المستخدم النموذج، وجود ID يجعل API تعدّل بدل أن تضيف.

## عند الضغط على Delete
- نطلب تأكيدًا من المستخدم.
- نرسل `DELETE` إلى API.
- نعيد تحميل الجدول بعد النجاح.

---

# الجزء 7: `categories.js` و`users.js`

هما مبنيان على نفس النمط:
- جلب البيانات من API.
- بناء صفوف الجدول.
- إرسال النموذج باستخدام JSON عبر `JSON.stringify()`.
- استخدام `POST` للإضافة و`PUT` للتعديل.
- استخدام `DELETE` للحذف.
- إظهار الأخطاء في الصفحة.

في `categories.js`، هذا الجزء:
```javascript
headers: { 'Content-Type': 'application/json' },
body: JSON.stringify(payload)
```
يخبر PHP أن جسم الطلب JSON، ثم يحول كائن JavaScript إلى نص JSON.

أما `users.js` فيرسل بيانات المستخدم بالطريقة نفسها، لكنه لا يرسل كلمة المرور إلى جدول العرض، ويتيح ترك حقل كلمة المرور فارغًا عند تعديل مستخدم.

---

# الجزء 8: كيف تختبرين الـ API بنفسك؟

1. شغّلي Apache وMySQL.
2. افتحي `http://localhost/OOP_CRUD_API/`.
3. افتحي صفحة Products.
4. افتحي أدوات المطور في المتصفح: F12 ثم Network.
5. أعيدي تحميل الصفحة.
6. اختاري الطلب `products.php`.
7. راقبي:
   - Request URL: العنوان الذي طلبته JavaScript.
   - Request Method: مثل GET.
   - Response: JSON الذي أعاده PHP.
8. جرّبي إضافة منتج، ثم راقبي طلب POST.
9. جرّبي حذف منتج، ثم راقبي طلب DELETE.

إذا فتحتي `http://localhost/OOP_CRUD_API/api/products.php` مباشرة في المتصفح، سترين JSON. هذا طبيعي: الـ API لا تعرض جدول HTML؛ هي تعيد بيانات.

## أهم فكرة تحفظينها بالفهم
HTML تعرض الواجهة، وJavaScript تطلب البيانات وتتفاعل مع المستخدم، وAPI في PHP تستقبل الطلب وتستدعي دوال قاعدة البيانات، و`db.php` ينفذ SQL، ثم ترجع API JSON إلى JavaScript.
