# FleaMarketApp(フリマアプリ)  
  
## 環境構築  
### Dockerビルド  
 1. クローンを生成  
 ``` bash
 git clone git@github.com:fujiwara-k0814/first-mock-project.git  
 ```
 2. DockerDesktopアプリを立ち上げる  
 3. Dockerをビルドする  
 ``` bash
 docker compose up -d --build  
 ```
※MySQLはOSの都合上、各人でファイルを編集  
  
  
### Laravel環境構築  
 1. item_imagesのファイルを生成  
 ``` bash
 mkdir src/storage/app/public/item_images
 ```
 2. 商品画像をコピー  
 ``` bash
 cp -r src/database/seeders/images/* src/storage/app/public/item_images
 ```
 3. PHPコンテナに入り、bashシェルを起動  
 ``` bash
 docker compose exec php bash
 ```
 4. composerをインストール  
 ``` bash
 composer install
 ```
 5. .env.exampleファイルから.envファイルを作成  
 ``` bash
 cp .env.example .env
 ```
 6. 環境変数を設定  
 ``` text
 DB_CONNECTION=mysql
 DB_HOST=mysql
 DB_PORT=3306
 DB_DATABASE=laravel_db
 DB_USERNAME=laravel_user
 DB_PASSWORD=laravel_pass
 ```
 7. Stripeのダッシュボードへアクセスし、シークレットキーを取得。環境変数を設定
 ``` text
 STRIPE_SECRET_KEY=sk_test_************ //←取得したキーを設定
 ```
 8. .env.testing.exampleファイルから.env.testingファイルを作成  
 ``` bash
 cp .env.testing.example .env.testing
 ```
 9. Stripeのダッシュボードへアクセスし、シークレットキーを取得。環境変数を設定
 ``` text
 STRIPE_SECRET_KEY=sk_test_************ //←取得したキーを設定
 ```
 10. Stripeのテスト環境をONにする
 11. アプリケーションキーの生成  
 ``` bash
 php artisan key:generate
 ```
 12. テストファイルのキーを生成  
 ``` bash
 php artisan key:generate --env=testing
 ```
 13. マイグレーションの実行  
 ``` bash
 php artisan migrate
 ```
 14. storageディレクトリのアップロードファイルを公開  
 ``` bash
 php artisan storage:link
 ```
 15. シーディングの実行  
 ``` bash
 php artisan db:seed
 ```
 16. Stripe連携用のライブラリをインストール  
 ``` bash
 composer require stripe/stripe-php
 ```
  
### メール認証  
MailHogというツールを使用しています。  
.envの設定が以下になっているか確認してください。  
尚、MAIL_FROM_ADDRESSは任意のメールアドレスを入力してください。  
 ``` bash
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=example@example.com
MAIL_FROM_NAME="${APP_NAME}"
 ```
  
  
## 使用技術  
・PHP 8.1  
・Lravel 8.83  
・MySQL 8.0  
  
  
## ER図  
<img width="644" height="709" alt="image" src="https://github.com/user-attachments/assets/6b62eeee-ebc2-4b37-b516-71526692f4b1" />  
  
  
## URL  
・開発環境：http://localhost/  
・phpMyAdmin：http://localhost:8080/  
・MailHog：http://localhost:8025/  
  
  
## テストアカウント  
  
・CO01 - CO05 出品ユーザー  
name : テストユーザー1  
email : user1@example.com  
password : password  
  
・CO06 - CO010 出品ユーザー  
name : テストユーザー2  
email : user2@example.com  
password : password  
  
・未出品ユーザー  
name : テストユーザー3  
email : user3@example.com  
password : password  
  
### テストコマンド  
``` bash
php artisan test tests/Feature
```
  
  
## 折り込み内容  
・追加UI  
・追加機能  
・追加分データベース  
・変更分ダミーデータ  
・レスポンシブ対応(768-850px)  
・追加分テスト  