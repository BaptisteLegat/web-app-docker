# web-app-docker

## Diagramme de l'infrastructure Docker
![alt text](<diagramme docker.png>)

## Diagramme de l'applicatiation
![alt text](<diagramme projet.png>)

# Installation du projet

## Prérequis

- Docker: https://docs.docker.com/get-docker/
- Docker-compose : https://docs.docker.com/compose/install/

## Installation

1. Cloner le projet

```bash
git clone https://github.com/BaptisteLegat/web-app-docker.git
```

2. Se déplacer dans le dossier du projet

```bash
cd web-app-docker
```

3. Lancer le projet

```bash
docker-compose up -d --build
```

4. Accéder au container FMP

```bash
docker exec -it web-app-docker-fpm-1 bash
```

5. Installer les dépendances

```bash
composer install
```

6. Créer les tables

```bash
php bin/console doctrine:migrations:migrate
```

7. Sortir du container

```bash
exit
```

8. Accéder à l'application web

```
http://localhost:10234/
```

9. Accéder à l'application phpMyAdmin

```
http://localhost:8089/
```

identifiants :
- user : app
- password : app

## Arrêter le projet

```bash
docker-compose down
```
