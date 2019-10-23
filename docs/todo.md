
## IN PROGRESS
- (T1) continuously upgrading of HLPR lib, annotate tested functions accordingly
- (T1) refactor & stabilization of tests execution while implementing:
    - investigate forbidding of start or end titles (Album title name, Song title name, Song filename, any tag, etc.) from a dot (".") : RESTRICTED_PREFIX, RESTRICTED_POSTFIX
    - keep Album tags with other Album data as $data['tags']
    - try to make Song system filename as shorter as possible:
        - extend meta tags (^1) from Album level to Song level automatically (^2)
            - (^1) ex: add "isExtendable" attr for Tag
            - (^2) while keeping Song Data
            - investigate extending from Artist level to Album level
                - (?) is Artist able to have meta tags (restrict OR allow with QA)
        - add " remix" & "remix " to blacklist (ex: replace all with "rmx")
            - then: wrap all those under "rmx" info tag
        - investigate " vs ", "vs.", " vol ", "vol.", " ver ", " version", "instrumental", "instr", "инстр", "інстр" for unifying
            - declare allowed AND/OR restricted
        - watch for max length of Songs' Filepaths (while resetting Catalog?)
    - while updating Metadata, fetch Artists those:
        - have manually added correspondent prefix (ex: "_")
        - have no correspondent mark (^1) about updated metadata (^2)
            - (^1) ex: build number AND/OR timestamp
            - (^2) ex: timestamp in index file OR some general log file on BHV level



## BACKLOG
- (F2) Metadata inheritance
    - Song Metadata is inherited from Album level and can be overwritten by its own if tag names matched
    - Album Metadata is inherited from Artist level and can be overwritten by its own if tags matched
    - fail Assert in correspondent Test if every Song has the same "c=" (...) tag
        - create new Test if there is such need
        - explore for more similar cases
        - suggest solution in Assert err msg
            - ex: in case of full "c=" repeat: "move tag to the Album name level to be extended accordingly

- (T2) Code clean-up
    - move "unit.php" technical functions to the HLPR lib level
    - reduce all TODOs inside project (the own ones)
    - add annotation to every head variable of every model object
    - make sure every thrown in the method Exception case is described in annotations to this method

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
