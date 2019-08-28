# Flip.Id Mini Project Test

A minimal slightly-big Flip disbursement client

## Installation

### Setup Config & Autoloader

```sh
cp ./config/database.example.php ./config/database.php &&
composer install
```

### Run Database Migration

```sh
php migration.php
```

## Usage

### Create Disbursement

```sh
php disbursement.php
```

### Show & Update Disbursement Status

```sh
php disbursementStatus.php {transaction_id}
```
