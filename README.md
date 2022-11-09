## Assgiment Restaurant List 


## 📦 Installation

```bash
$ git clone https://github.com/Atww/RestaurantsServices.git
$ cd RestaurantsServices
$ composer install
$ php artisan key:generate
$ npm install
```
## 🔨 Usage


Copy `.env.example` and change Name To `.env` 

```diff
+APP_GOOGLEMAP_KEY=***** REQUIRED Google Map API KEY ****
+CACHE_TIMER_SECOND=120
```
```bash
$ php artisarn serve
$ npm run dev
```