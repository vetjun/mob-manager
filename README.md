# mob-manager


## Installation

Clone Project ->
`
git clone https://github.com/vetjun/mob-manager.git
`

Install Composer Packages ->
`
composer install
`

Create A Environment File Named '.env'->
`
You can copy from .env.example file
`


Generate Key ->
`
php artisan key:generate
`

Migrate Tables
`
php artisan migrate
`

To Create Fake Records For Some Tables

`
php artisan db:seed
`

To Clear Cache
`
php artisan cache:clear
`

## API

Api software manages data creating, updating and making some actions on them.

Registering accounts with http://127.0.0.1:8000/mob/register endpoint

Purchasing applications and subscribes with http://127.0.0.1:8000/mob/purchase endpoint

Canceling subscriptions with http://127.0.0.1:8000/mob/cancel endpoint


### Endpoints
1.
    POST -> http://127.0.0.1:8000/mob/register

    Parameters -> device_uid, app_id, language, operation_system(ios, google)

2.  
    POST -> http://127.0.0.1:8000/mob/application
    
    Parameters -> app_id, name, description

3.
    POST -> http://127.0.0.1:8000/mob/credential

    Parameters -> app_id, username, password, provider (ios, google)

4.
    POST -> http://127.0.0.1:8000/mob/purchase

    Parameters -> client_token, receipt

5.
    GET -> http://127.0.0.1:8000/mob/subscriptions

    Parameters -> client_token

6.
    GET -> http://127.0.0.1:8000/mob/cancel

    Parameters -> client_token


## Worker
    
Worker runs as a command line application with artisan tool.

Worker renews expired subscriptions with same receipt values. Purchases again with operating system provider api servers.

Updates subscriptions table

### To Run Worker

`
php artisan subscriptions:renew
`

It generates queue task messages to database.

So you need to run consumer to process queue messages

`
php artisan queue:work --queue=renew_subscriptions_0
`

To generate task messages with load balance

`
php artisan subscriptions:renew --loadBalanceQueueSize=4
`

And Consume Them You Should Create 4 consumer processes

`
php artisan queue:work --queue=renew_subscriptions_0
`

`
php artisan queue:work --queue=renew_subscriptions_1
`

`
php artisan queue:work --queue=renew_subscriptions_2
`

`
php artisan queue:work --queue=renew_subscriptions_3
`
