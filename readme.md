# REST API - Uživatelé a články (Nette + Docker + JWT)

Toto je ukázková implementace jednoduchého REST API v PHP 8.2 postaveného na frameworku **Nette**, určeného pro správu uživatelů a článků. API využívá autentizaci pomocí JWT a role pro řízení přístupu. Povolené role jsou reader, author a admin.

## ✅ Funkce

- **Registrace a přihlášení uživatelů** (JWT)
- **CRUD operace** pro články a uživatele
- **Role-based access control** (admin, author, reader)
- **REST API** přístupné přes HTTP
- **Spustitelné přes Docker Compose**

[//]: # (- **Testy** pomocí Nette Tester)

## 🛠 Požadavky

- Docker a Docker Compose

## 🚀 Spuštění projektu

1. Naklonujte repozitář:

```
git clone https://github.com/KonicekDavid/api-test.git
cd api-test
```
2. Vytvořte vlastní konfigurační soubor **config/local.neon** a vložte do něj následující kód, přičemž **secretKey** nahraďte vlastním tajným klíčem:

```
parameters:
    jwt:
        secret: 'secretKey'
```

3. Spusťte Docker:
```
docker-compose up --build
```
4. Aplikace bude dostupná na: http://localhost:8080

## 🗃 Databáze
Používá se SQLite. Po spuštění kontejneru se vytvoří databázový soubor database.sqlite.

## 🔑 Autorizace (JWT)
Po přihlášení získejte token a přidávejte jej do HTTP hlavičky jako:
```
Authorization: Bearer <token>
```

## 📍 Endpointy
```
POST /auth/register - registrace nového uživatele (povinné údaje - email, password, name)
POST /auth/login - přihlášení uživatele - vrací JWT token (povinné údaje - email, password)

# Pouze admin role
GET /users - vrací seznam uživatelů
GET /users/{id} - vrací konkrétního uživatele
POST /users - vytváří nového uživatele
PUT /users/{id} - upravuje konkrétního uživatele
DELETE /users/{id} - maže konkrétního uživatele

# Různá omezení rolí
GET /articles - vrací seznam článků (všechny role)
GET /articles/{id} - vrací konkrétní článek (všechny role)
POST /articles - vytváří nový článek (pouze role admin nebo author)
PUT /articles/{id} - upravuje konkrétní článek (pouze role admin nebo author daného článku)
DELETE /articles/{id} - maže konkrétní článek (pouze role admin nebo author daného článku)
```

## 📚 Příklady volání API
1. Registrace uživatele
```
POST /auth/register
Content-Type: application/json

{
  "email": "jan.novak@test.cz",
  "password": "password",
  "name": "Jan Novák",
  "role": "author"
}
```
2. Přihlášení uživatele
```
POST /auth/login
Content-Type: application/json

{
  "email": "jan.novak@test.cz",
  "password": "password"
}
```
Odpověď:
```
{
"token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6NCwiZW1haWwiOiJkYXZpZEB0ZXN0aWlpaWsuY3oiLCJyb2xlIjoicmVhZGVyIiwiZXhwIjoxNzQ1NTI2Nzg2fQ.jAfxzcynsFi4k3GH6Bg6tGk_uzxEppVv6eMqSSJjucg"
}
```
3. Vytvoření článku
```
POST /articles
Authorization: Bearer <token>
Content-Type: application/json

{
  "title": "První článek",
  "content": "Obsah článku..."
}
```