
## IN PROGRESS
- `T2` unifying the Song entity
- `F2` as a BHV keeper, i want to manage freestanding Songs (without Artists) as well (ex: POLLEN)
    - `td` located on the same level as BHV
    - `qa` covered with "artistsAreNotDuplicated" test at least
- `T3` stabilize Albums downloading




## BACKLOG

###### major
- `T2` `qa` Move "$this->assert" from Test Cases to standalone (as trait?) Util
- `T2` `qa` Integration tests to cover features
- `F2` Statistics:
    - in BHV, qty of:
        - Artists (total)
        - Albums per Artist
        - Songs per Album
        - Songs per Artist



    ...

- `F2` Metadata inheritance
    - Song Metadata is inherited from Album level and can be overwritten by its own if tag names matched
    - Album Metadata is inherited from Artist level and can be overwritten by its own if tags matched
        - `??` add "isExtendable" attr for Tag
    - fail Assert in correspondent Test if every Song has the same "c=" (...) tag
        - create new Test if there is such need
        - explore for more similar cases
        - suggest solution in Assert err msg
            - `ex` in case of full "c=" repeat: "move tag to the Album name level to be extended accordingly

###### minor
- `T3` describe all tests in its annotations
- `F3` single-sided & dual-sided Relations between Artists (ex: "audioslave" & "rage against the machine")
- `F3` add references to existent in BHV Artists inside additional info tags (ex: "ft=REF", "c=REF")
- `F3` improve Metadata (ex: add rating, original date, genres, bitrate, etc.)
- `F3` as a BHV keeper, i want to have backups to prevent the loss of data (mirror?)
- `F3` Artist logo
- `T3` implement CLI
- `T3` implement Dependency Injection
- `T3` describe every thrown Exception in annotations, make all Exceptions named
- `T3` investigate if there is a need to replace autoloading with declaration of uses
- `T3` investigate if it makes sense to move executable PHP to vendor
- `T3` upgrade PhpUnit

###### trivial
- `F4` Artist media: Photos
    - set "limit total size (MB) per Artist" to config
- `F4` Artist media: Clips
    - set "limit total size (MB) per Artist" to config
- `F4` Metadata validation by comparing it to external Resources
    - `ex` "discogs", "wikipedia"
    - titles (or any other Metadata) can be whitelisted in index file in order to be overridden
    - titles (or any other Metadata) can be blacklisted in index file in order to be ignored
- `T4` investigate Factory pattern usage for Songs, Albums, Artists
- `F4` solid index strategy:
    - Artist index file (ex: last Album listened)
    - Album index file (ex: Song title placeholder)
    - use Catalog as BHV index file
- `F4` Multi Extensions support
    - `ex` ".flac", ".png"
- `F4` integration with "di.fm" wishlist (several accounts)



## NOTATIONS

   | notation    | meaning             | comments      |
   |-------------|---------------------|---------------|
   |  `B`        | bug                 |               |
   |  `F`        | feature             |               |
   |  `T`        | task                |               |
   |`1`, `2`, ...| priority            |from `0` to `4`|
   |             |                     |               |
   |    `ex`     | example             |               |
   |    `td`     | tech design         |               |
   |    `qa`     | quality assurance   |               |
   |    `??`     | open question       |               |


