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
 - [ ] Status, LND starting up (wait for sync), Wallet is encrypted, ask for decryption key etc

## Milestone 2
 - [ ] Design / UX
 - [X] Local currency
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

## _Production Docker-compose.yml file_ (not yet!)
```yaml
version: "2.1"

services:
  lnd:
    image: lightningsale/docker-lnd
    environment:
      - EXTERNALIP=92.221.98.237
      - EXTERNALPORT=9736
      - RPCUSER=lightningsale
      - RPCPASS=lightningsale
      - NETWORK=testnet
      - CHAIN=bitcoin
      - DEBUG=info
      - REDIS_URL=redis
    volumes:
      - lnd:/root/.lnd
    ports:
      - 9736:9736
      - 127.0.0.1:10009:10009
    expose: ["8080"]
    entrypoint: ["./start-lnd.sh"]
    hostname: lnd
    command:
      - "--neutrino.active"
      - "--neutrino.connect=faucet.lightning.community"
      - "--autopilot.active"
      - "--no-macaroons"
      - "--peerport=9736"
  pos:
    image: lightningsale/lightning-sale
    ports:
      - 80:80
    environment:
      - APP_ENV=prod
      - APP_DEBUG=false
      - APP_SECRET=22375fd9fdfe7235fb7386334f6e9632
      - DATABASE_URL=mysql://root:abcd1234@mysql:3306/lightningsale
      - LND_HOST=lnd
      - LND_PORT=8080
      - EXTERNALIP=92.221.98.237
      - EXTERNALPORT=9736
      - RPCUSER=lightningsale
      - RPCPASS=lightningsale
      - REDIS_URL=redis
    depends_on:
      - mysql
      - lnd
    volumes:
      - .:/var/www
      - lnd:/var/www/var/lnd
  mysql:
    image: mysql
    ports:
      - 127.0.0.1:3307:3306
    volumes:
      - mysql:/var/lib/mysql
    environment:
      - MYSQL_ROOT_PASSWORD=abcd1234
  redis:
    image: redis
volumes:
  lnd: ~
  mysql: ~

```

## LightningSale TODO:
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
