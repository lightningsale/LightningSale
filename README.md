# Lightning Sale Point of Sale

Set up the .env file or environment variables for database and LND config!
Or use github.com/lightningsale/LightningSale and `docker-compose up`

## LightningShop TODO:
 - [ ] Copywriting
 - [ ] Design/Style

## Setup
 - [ ] Setup a database
 - [ ] Configure settings with environment variabelse or configure them in the `.env` file
 - [ ] Configure wallet / kommunikasjon med LND
 - [ ] Configure exchange 
   - [ ] Select Currency
   - [ ] Select transaction fee (for customer)
   - [ ] Settlement address
   - [ ] Settlement (immediatly, on demand, periodic)
 - [ ] Create first user
 - [ ] Setup script?

## Pages / TODO:
 - [ ] Login
 - [ ] Customer details (whitelabel stuff
   - [ ] Node/Wallet details/Status
     - [ ] Channe funding
     - [ ] Wallet funding
   - [ ] Setup user accounts
 - [ ] User details
 - [ ] Dashboard
   - [ ] Create payment request (invoice)
   - [ ] List payments (status) (Select wich payment to show on screen)
   - [ ] Payment details
   - [ ] Screen ID (and what is displayed)
 - [ ] Payment request details screen (Consumer facing)
   - [ ] Login (With screen ID)
   - [ ] Whitelabel "default" screen
   - [ ] Payment request details:
     - [ ] QR-Code
     - [ ] Whitelabel
     - [ ] Serialized payment request
     - [ ] Price in Bitcoin / Selected currency
## Domain model
 - [ ] User (base, regular, owner)
 - [ ] Screens (one pr logged in user?)
 - [ ] Payments
