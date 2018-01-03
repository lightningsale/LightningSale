# Lightning Sale Point of Sale

Set up the .env file or environment variables for database and LND config!
Or use github.com/lightningsale/LightningSale and `docker-compose up`

**This software is alpha only, do not use with real money!**
## Known issues:
- `closeChannel` doesn't work
  - Also missing force close functionality
- `lookupInvoice` doesnt work 

## Milestone 1
 - [ ] Set up users and authentication (Security!)
 - [ ] Configure (Local currency, invoice timeout)
 - [X] Funding
 - [ ] Peers (Connecting doesn't work so good)
 - [ ] Channels (Missing open/close channel, missing support in LND REST)
 - [X] Transactions
 - [ ] Invoices
    - [X] Create invoice
    - [X] Pending invoices
    - [ ] Remove old unpaid invoices
    - [ ] Invoice details (missing support in LND REST)

## Milestone 2
 - [ ] Design / UX
 - [ ] Local currency
 - [ ] Realtime information
 - [ ] Secure deployments / Auditing
 - [ ] Streamline deployments (collect all env variables in docker-compose.yml etc)
 - [ ] Backups and Recovery
 - [ ] Encrypted wallet
 
## Milestone 3
 1. [ ] Email notifications

## First setup:
 - git clone https://github.com/lightningsale/LightningSale
 - Copy `.env.dist` to `.env` and modify `EXTERNALIP`
 - docker-compose up
 - docker-compose exec pos composer install
 - docker-compose exec pos php bin/console doctrine:database:create
 - docker-compose exec pos php bin/console doctrine:schema:update --force
 - docker-compose exec pos php bin/console app:create:user <-- Create first user

## LightningShop TODO:
 - [ ] Copywriting
 - [ ] Design/Style
 - [ ] Figure out how to copy required certificates from LND to POS
 - [ ] Figure out how to use Macaroons
 - [X] Move POS repo to LightningSale repository
 - [X] Use `LND`s own Dockerfile's https://github.com/lightningnetwork/lnd/tree/master/docker
 - [ ] Use Docker Secrets
 - [ ] Security!

## Components:
 - [X] -LND gRPC Client- Use LND Rest Client
 - [X] Auth Component (Handle owners and Users)
 - [X] QR Code
 - [X] Payments Dashboard


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
 - [X] Login
 - [ ] 2-Factor authentication
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
 - [X] User (base, Cashier, Merchant)
 - [ ] Screens (one pr logged in user?)
 - [ ] Payments
