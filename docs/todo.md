
## IN PROGRESS
- (T2) continuously upgrading of HLPR lib, annotate tested functions accordingly
- (T2) Code clean-up
    - make all verifications inside "qa/tests/abstract.php" argument independent
        - ex: "$album->getTitle();" can be changed to "$this->title;"
    - move "unit.php" technical functions to the HLPR lib level
    - make sure every thrown in the method Exception case is described in annotations to this method

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
- (F2) as a BHV keeper, i want to manage freestanding Songs (without Artists) as well (POLLEN)
    - (td) located on the same level as BHV
    - formatting customization is supported
    - (qa) covered with "artistsAreNotDuplicated" test at least
- (T2) HLPR lib from app to vendor
- (F2) Artist index file (ex: last Album listened)
- (F2) Album index file (ex: Song title placeholder)
- (F2) Make Catalog as BHV index file

- (F3) single-sided & dual-sided relations between Artists (ex: "audioslave" & "rage against the machine")
- (F3) add references to existent in BHV Artists inside additional info tags (ex: "ft=REF", "c=REF")
- (F3) extend Metadata (ex: rating, original date, genres, etc.)
- (F3) as a BHV keeper, i want to have backups to prevent the loss of data (mirror?)
- (T3) investigate if there is a need to replace autoloading with declarating of uses

- (F4) Photos keeping
- (F4) Clips keeping
- (F4) validating Metadata (via "discogs", "wikipedia", etc.)
- (F4) as a BHV keeper, i want to have Tests automatically executed by schedule with reporting readable results in case of failures
- (F4) support multi Extensions
- (T4) investigate patterns usage for Songs, Albums, Artists (ex: Factory)
- (T4) execute Catalog tests before the Catalog update
- (F4) integrate with di.fm wishlist (several accounts)
- (T4) add Static Tests (ex: PSR)
