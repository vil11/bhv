
## IN PROGRESS
- (T2) continuously upgrading of HLPR lib, annotate tested functions accordingly
- (F2) QC integration:
    - run session from "features" facade
    - run session (for "marked to be updated" Artists only) when the "metadata updating" is finished
        - (i) in order to stabilize potential tests fails separately from the massive stable build

## BACKLOG
- (F2) Metadata inheritance
    - Song Metadata is inherited from Album level and can be overwritten by its own if tag names matched
    - Album Metadata is inherited from Artist level and can be overwritten by its own if tags matched
        - (?) add "isExtendable" attr for Tag
    - fail Assert in correspondent Test if every Song has the same "c=" (...) tag
        - create new Test if there is such need
        - explore for more similar cases
        - suggest solution in Assert err msg
            - ex: in case of full "c=" repeat: "move tag to the Album name level to be extended accordingly
- (F2) implement index files:
    - Artist index file (ex: last Album listened)
    - Album index file (ex: Song title placeholder)
    - use Catalog as BHV index file
- (F2) as a BHV keeper, i want to manage freestanding Songs (without Artists) as well (ex: POLLEN)
    - (td) located on the same level as BHV
    - formatting customization is supported
    - (qa) covered with "artistsAreNotDuplicated" test at least
- (T2) move HLPR lib from app to vendor

- (F3) QC integration:
    - run tests directly on GitHub on every commit push?
    - report readable results in case of not success
- (F3) single-sided & dual-sided Relations between Artists (ex: "audioslave" & "rage against the machine")
- (F3) add references to existent in BHV Artists inside additional info tags (ex: "ft=REF", "c=REF")
- (F3) improve Metadata (ex: add rating, original date, genres, etc.)
- (F3) as a BHV keeper, i want to have backups to prevent the loss of data (mirror?)
- (T3) implement CLI
- (T3) describe every thrown Exception in annotations
- (T3) investigate if there is a need to replace autoloading with declaration of uses
- (T3) investigate if it makes sense to move executable PHP to vendor

- (F4) Photos keeping
    - set "limit summary size (MB) per Artist" to config
- (F4) Clips keeping
    - set "limit summary size (MB) per Artist" to config
- (F4) Metadata validation by comparing it to external Resources
    - ex: "discogs", "wikipedia"
    - titles (or any other Metadata) can be whitelisted in index file
- (F4) Multi Extensions support (ex: ".flac", ".png")
- (T4) investigate Factory pattern usage for Songs, Albums, Artists
- (F4) integration with "di.fm" wishlist (several accounts)
