
## IN PROGRESS
- continuously upgrading of HLPR lib, tag tested functions accordingly
- refactor & stabilization of tests execution while implementing:
    - investigate forbidding of start or end titles (album title name, song title name, song filename, any tag, ...) from a dot (".") : RESTRICTED_PREFIX, RESTRICTED_POSTFIX
    - keep Album tags with other Album data as $data['tags']
    - make song system filename shorter:
        - extend meta tags (^1) from Album level to Song level automatically (^2)
            - (^1) ex: add "isExtendable" attr for Tag
            - (^2) while keeping Song Data
            - investigate extending from Artist level to Album level
                - (?) is Artist able to have meta tags (restrict OR allow with QA)
        - add " remix" & "remix " to blacklist (ex: replace all with "rmx")
            - then: wrap all those under "rmx" info tag
        - investigate " vs ", "vs.", " vol ", "vol.", " ver ", " version", "instrumental", "instr", "инстр", "інстр" for unifying
            - declare allowed AND/OR restricted
        - watch for max length of Songs' Filepaths (while reseting Catalog?)
    - while updating Metadata, fetch Artists those:
        - have manually added correspondent prefix (ex: "_")
        - have no correspondent mark (^1) about updated metadata (^2)
            - (^1) ex: build number AND/OR timestamp
            - (^2) ex: timestamp in index file OR some general log file on BHV level



## BACKLOG
- (T2) reduce all TODOs inside project (the own ones)
- (T2) add annotation to every head variable of every model object
- (T2) make sure every thrown in the method Exception case is described in annotations to this method
- (F2) Song Metadata is inherited from Album level & can be overwritten by its own
- (F2) as a BHV keeper, i want to have data backups to prevent the loss of data
- (T2) move "unit.php" technical functions to the "hlpr" lib level
- (F3) dual-sided relations between artists (ex: "audioslave" & "rage against the machine")
- (F3) pseudo links to existent in bhv artists inside additional info tags (as "feat ...", "... cover", etc)
- (F3) extend metadata with: rating, original date, genres, ...
- (F4) photos processing
- (F4) clips processing
- (F4) validating metadata via "discogs", "wikipedia", etc
- (T4) possibility to run tests by schedule with reporting readable results in case of failures
- (T4) investigate Factory pattern usage for Songs, Albums, Artists
- (T4) execute Catalog tests before the Catalog update
