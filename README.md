# مركز تكامل مؤسسي
## Enterprise Integration Hub

**Portfolio Demo Project** | المستوى: **احترافي عالي** | المؤلف: [Eng-7ussamEltayb](https://github.com/Eng-7ussamEltayb)

### الفكرة
نواة مركز تكامل: مصادقة بمفتاح API، استقبال Webhooks، طابور مهام، وسجل تدقيق، مع Worker بايثون للمعالجة.

### التقنيات
PHP API + Python worker

### المميزات
- X-API-KEY auth
- Webhook inbound
- Job queue
- Audit log
- Python worker

### طريقة التشغيل
1. cd php-hub/public && php -S localhost:8080
2. curl -X POST http://localhost:8080/webhooks/inbound -H "X-API-KEY: demo-key-123" -H "Content-Type: application/json" -d "{\"type\":\"asset.updated\"}"
3. python ../python-worker/worker.py
4. curl http://localhost:8080/jobs -H "X-API-KEY: demo-key-123"

### هيكل المشروع
```
php-hub/
python-worker/worker.py
README.md
```

### ملاحظات
- هذا مشروع تعليمي/معرض أعمال (Portfolio)، وليس بالضرورة نظام إنتاج كامل.
- لا تضع أسراراً حقيقية داخل المستودع.

### الرخصة
MIT — للاستخدام التعليمي والعرض المهني.
