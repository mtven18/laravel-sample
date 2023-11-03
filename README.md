# Laravel App Sample

## Setup project

Run `composer install` for install project dependencies. If you don't have the local composer than run:

```shell
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```

Copy the `.env.example` to `.env` file and change variables if need.

Don't forget set alias for the Sail for each shell sessions:

```shell
source .bashrc
```

Run docker containers:

```shell
sail up -d
```

and initialize the project:

```shell
sail artisan kye:gen
sail artisan migrate
```

## API doc

API documentation files placed in `resources/docs/api`

## Testing

Run:

```shell
sail test
```
