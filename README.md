# Metalrom Step Discounts

WooCommerce plugin care permite definirea si aplicarea discounturilor progresive si a tarifelor de transport personalizate per produs, in functie de praguri de cantitate.

## 🔧 Functionalitati

* Setare discount procentual per prag de cantitate
* Tarif de transport per prag (inclusiv "Gratuit")
* Evidentierea pragului activ in frontend (cu update in timp real)
* Calcul automat in functie de cantitatea introdusa + cantitatea din cos
* Actualizare automata a pretului afisat in pagina produsului
* Suport pentru TVA inclus
* Compatibil cu tema Woodmart si sticky add-to-cart
* Interfata dinamica bazata pe AJAX si jQuery

## 📦 Instalare

1. Cloneaza acest repository in `wp-content/plugins/metalrom-step-discounts`
2. Acceseaza panoul de administrare WordPress si activeaza pluginul
3. In pagina de editare a unui produs, adauga praguri de discount in metaboxul "Metalrom Step Discounts"

## 🖥️ Utilizare

* In frontend, clientul va vedea tabelul cu discounturi disponibile
* La modificarea cantitatii, se va actualiza automat:

  * Linia activa din tabel
  * Pretul afisat
  * Textul cu "✅ Prag activ"
* Se tine cont si de cantitatea deja aflata in cos

## 🛠️ Structura

* `includes/admin-fields.php` – campurile din backend
* `includes/frontend-display.php` – logica de afisare si calcul in frontend
* `assets/frontend.js` – script jQuery pentru actualizare dinamica
* `assets/frontend.css` – stiluri pentru evidentiere

## 🧪 Exemplu de configurare

| Cantitate minima | Discount (%) | Transport |
| ---------------- | ------------ | --------- |
| 5 buc            | 10%          | 55 lei    |
| 10 buc           | 12%          | 60 lei    |

## 🧩 Compatibilitate

* WooCommerce >= 5.0
* WordPress >= 5.8
* Tema: testat si optimizat pentru Woodmart

## 📄 Licenta

GPLv2 sau ulterior – vezi fisierul LICENSE

## 🤝 Contributii

Pull requests sunt binevenite. Deschide un issue pentru buguri sau functionalitati noi.

## 🔗 Linkuri utile

* [WooCommerce Plugin Developer Docs](https://developer.woocommerce.com/)
* [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
