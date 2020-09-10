# Description

The service provides a booking system to manage cleaners when the companies give a cleaning service to their clients.

# Requirements

PHP 7.3 or higher,
Composer,
SQL Database


# Getting Started

To setup the project quickly follow the instructions below.

### Prerequisites

This project requires the *composer* package manager and `7.3` or greater versions of `PHP`.
* To install the *composer*: `https://getcomposer.org/download/`
* To install the `PHP` from official documentation: `https://www.php.net/manual/en/install.php`


## Installation

#### 1. Clone the repository
```sh
git clone https://github.com/muratbaskicioglu/mop-masters.git
```
#### 2. Install packages
```sh
composer install
```
#### 3. [Make configurations](#configurations)

#### 4. Creating the database and tables/schemas
```sh
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```
#### 5. Loading fixtures(sample data)
* Write auto generated *sample data* into `Company` and `Cleaner` tables by executing:
```sh
php bin/console doctrine:fixtures:load
```
#### 6. Start the server
```sh
symfony server:start
```
That's it. The API documentation can be found at: http://localhost:8000/api/doc

## Configurations

* All configurations below needs to be set inside `.env` file.
Also, you can create `.env.local` file to prevent remote changes.

#### Database

Set your database connection:

```
DATABASE_URL=<database_connection_string>
```

#### Application

You should set a timezone according to the a service area:

```ENV
TIME_ZONE=Asia/Dubai
```

#### Booking Service

Provide time limits for service available hours to the Booking service:

```ENV
BOOKING_DATE_FORMAT=            # app's default date format(Y-m-d)
HOLIDAY_OF_WEEK_IN_NUMBER=      # day of week for holiday(5)
BOOKING_TIME_FORMAT=            # time format(H:i:s)
BOOKING_START_TIME_STRING=      # service start time(08:00:00)
BOOKING_END_TIME_STRING=        # end time 22:00:00
```

# Usage

This service has a few endpoints to fetch cleaners info and previously created bookings, also make a new booking, and update them with new dates. The project already has an automatically generated API documentation but I will give below short explanation about services.

#### Get cleaners' list with their linked companies

You can use `/cleaners` with `GET` to get a list of all cleaners.

#### Get booking dates of specified cleaner to know what times the cleaner are available

Use `/cleaners/{cleanerId}/unavailable-times` with `GET` to get unavailable date times of specific cleaner.

#### Create bookings with available cleaners

Use `/bookings` with `POST` and booking detail parameters. You should specify which cleaners(`cleanerIds`) you would book within a specified `date`, `startTime`, and `duration` of the cleaning service.

#### Update previously created booking dates

You can use `/bookings/{bookingId}` to update current booking date times and duration with giving same parameters.


## Model Structure

```JS
 Company                          Cleaner                             
+------+--------------+-----+    +------------+--------------+-----+
| id   | int          | PRI |    | id         | int          | PRI |
| name | varchar(255) |     |    | company_id | int          | MUL |
+------+--------------+-----+    | name       | varchar(255) |     |
                                 +------------+--------------+-----+
                               
 Booking                            BookingAssignment
+------------+----------+-----+    +------------+------+-----+
| id         | int      | PRI |    | id         | int  | PRI |
| start_date | datetime |     |    | cleaner_id | int  | MUL |
| end_date   | datetime |     |    | booking_id | int  | MUL |
+------------+----------+-----+    +------------+------+-----+
