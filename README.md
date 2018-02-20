# RedReader - No ticket, no work

Estudio on fetching tickets assigned to me on Redmine.

## Configure

Copy `config.dist.php` to `config.php`. 

Set Redmine base url and your personal API token.

## List issues

    bin/console red:issues

## Conky example

See [./conky/.conkyrc](./conky/.conkyrc) for an integration example with [Conky](https://github.com/brndnmtthws/conky).
