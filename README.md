# Project setup
## .env copy
You must copy `.env` file to `.env.local`
## DB installation
1. Launch the docker container : `docker-compose up -d`
2. Create the database : `php bin/console doctrine:database:create`
3. Migrate : `php bin/console d:m:m`
4. Load fixtures : `php bin/console d:f:l`
5. TA-DAAA !
# Important informations
## Fixtures
Log in as admin :
**username** : `admin@car-rental.fr`
**password** : `admin`
## Commits
User @jpoulain58 have less commits than @leRourou and @Arthurldvd because his PC was broken. 
Because of that, he helped with pair programming.
# Features
## For visitors
- Login
- Sign up
## For users
- Make a reservation
- Edit profile
- See user's reservations
- Log out
## As admin
- See users
- Edit users
- See a specific user's reservations
- See reservations
- Edit reservation
- See cars
- Edit cars
