~~~~~~~~~~~~~~~~~~~~~~~~
TypoScript-Konfiguration
~~~~~~~~~~~~~~~~~~~~~~~~

Setup
~~~~~

In der *pt_certification* gibt es zu TypoScript-Konfiguration 6 Dateien, welche in die setup.txt inkludiert werden. Damit
wird die Extension für das Frontend konfiguriert.

PtExtlist/\*:

    Die Extlist-Konfigurationen der Extension. (Siehe Kapitel **Listenkonfigurationen**

BaseConfiguration/ExtensionBase.txt

    Einstellungen betreffend die Funktionen im TYPO3/Extbase-Umfeld. In dieser Datei werden die Klassen auf die korrekten
    Datenbanktabellen gemappt, die Pfade zu den Views eingestellt (siehe **Konstanten**), aktiviert dass bei Änderungen
    an Fragen der Cache geleert wird und JavaScript-Libraries eingebunden.

BaseConfiguration/BaseSettings.txt

    Standardeinstellungen für Checks. Fast alle Optionen werden über die **Konstanten** konfiguriert, Ausnahmen:

    jumpNextQuestion:

        todo

    redirectAfterSave:

        todo

    redirectAfterDelete:

        todo

    displayContinueDialog:

        todo

BaseConfiguration/Prototype/Finisher.txt

    Finisher-Angaben für einen Check. Der Finisher wird aufgerufen, wenn der Check durchgeführt wurde. Mit diesem können z.B. zusätzliche
    Datenbank-Änderungen, Berechnungen oder Benachrichtigungen durchgeführt werden.

BaseConfiguration/ScoreManager.txt

    Ähnlich wie die Finisher kann hier eine Klasse zur Berechnung und Auswertung von Punkten angegeben werden.

BaseConfiguration/Prototype/ExerciseAnsweredChecker.ts

    Konfiguration für die Berechnung, ob Fragen richtig oder falsch sind. Es wird ein Mapping einer Berechner-Klasse auf einen
    Fragetyp vorgenommen.

Alle Konfigurationen außer *ExtensionBase.txt* werden auf QuestionnaireConfigurations kopiert, damit verschiedene Typen auf
verschiedene Art konfiguriert werden können. Diese QuestionnaireConfigurations sind in den Plugins auswählbar.



Konstanten
~~~~~~~~~~

Es gibt in der *pt_certification* mehrere Konstanten, die im Setup an den angegebenen Stellen verwendet werden.
Diese Konstanten sind alle im Konstanten-Editor in der Kategorie *PLUGIN.TX_PTCERTIFICATION* zu finden und können
dort eingestellt werden.

plugin.tx_ptcertification.view.templateRootPath:

    Das Verzeichnis, in dem die Templates der Extension liegen

    Standard-Wert: EXT:pt_certification/Resources/Private/Templates/

plugin.tx_ptcertification.view.partialRootPath:

    Das Verzeichnis, in dem die Partials der Extension liegen

    Standard-Wert: EXT:pt_certification/Resources/Private/Partials/

plugin.tx_ptcertification.view.layoutRootPath:

    Das Verzeichnis, in dem die Layouts der Extension liegen

    Standard-Wert: EXT:pt_certification/Resources/Private/Layouts/

plugin.tx_ptcertification.persistence.storagePid:

    Die pid der Seite, auf der die Elemente der pt_certification abgelegt werden sollen

    Standard-Wert: leer, also 0

plugin.tx_ptcertification.settings.numberOfExercises:

    Die Anzahl Fragen, die in einem Check nacheinander angezeigt werden soll.

    Standard-Wert: 10

plugin.tx_ptcertification.settings.displayJsCounter:

    Soll die Funktion aktiviert sein, dass ein JavaScript-Countdown im Check dargestellt werden soll? Wenn ja, wird nach
    60 Sekunden automatisch das gewählte Ergebnis der Frage übermittelt und die nächste Frage aufgerufen.

    Ein Wert von 1 entspricht 'ja' - Der Countdown wird eingeblendet.
    Ein Wert von 0 entspricht 'nein' - Der Countdown wird nicht eingeblendet.

    Standard-Wert: 1

plugin.tx_ptcertification.settings.displayQuestionId:

    Soll die ID der Frage bei der Frage dargestellt werden? Damit können bei Rückfragen die Benutzer direkt auf die Frage
    mit der angezeigten ID Bezug nehmen.

    Ein Wert von 1 entspricht 'ja' - Die ID wird angezeigt.
    Ein Wert von 0 entspricht 'nein' - Die ID wird nicht angezeigt.

    Standard-Wert: 1

plugin.tx_ptcertification.settings.questionnaireOnlyOneTime:

    Soll ein Benutzer einen Check zu einer bestimmten Kategorie nur ein einziges Mal durchführen können?

    Ein Wert von 1 entspricht 'ja' - Der Benutzer kann den Check zu einer Kategorie nur einmal durchführen.
    Ein Wert von 0 entspricht 'nein' - Der Benutzer kann jeden Check beliebig oft durchführen.

    Standard-Wert: 0

plugin.tx_ptcertification.settings.continueAbortedQuestionnaire:

    Soll ein beliebiger Check forgesetzt werden, wenn er unterbrochen wurde?

    Ein Wert von 1 bedeutet, dass ein Benutzer, wenn er während eines Checks auf eine andere Seite geht und danach erneut
    das Check-Plugin aufruft, eine Auswahl bekommt, ob er den Check fortsetzen möchte oder ob er den angefangenen Check
    löschen möchte.
    Ein Wert von 0 bedeutet, dass ein unterbrochener Check beim nächsten Aufruf des Plugins ignoriert wird, also eine neue
    Kategorieauswahl zur Verfügung gestellt bekommt.

    Standard-Wert: 0

plugin.tx_ptcertification.settings.continueAbortedQuestionnaireOfCategory:

    Soll ein Check fortgesetzt werden, wenn er unterbrochen wurde und danach die gleiche Kategorie gewählt wurde?

    Ein Wert von 1 bedeutet, dass ein Benutzer, wenn er Kategorie wählt, für die er einen unterbrochenen Check hat,
    diesen Check fortsetzt.
    Ein Wert von 0 bedeutet, dass der Benutzer einen neuen Check der gewählen Kategorie bekommt.

    Standard-Wert: 0

plugin.tx_ptcertification.settings.displayBackButton:

    Soll im Check ein Zurück-Button angezeigt werden, mit dem man zur vorigen Frage zurückkehren kann?

    Ein Wert von 1 bedeutet, dass der Zurück-Button eingeblendet wird.
    Ein Wert von 0 bedeutet, dass der Zurück-Button nicht eingeblendet wird.

    Standard-Wert: 0

plugin.tx_ptcertification.settings.displayCategorySelector:

    Soll der Kategorie-Auswahl-Dialog eingeblendet werden?

    Ein Wert von 1 bedeutet, dass der Auswahl-Dialog eingeblendet wird.
    Ein Wert von 0 bedeutet, dass der Auswahl-Dialog nicht eingeblendet wird. Es wird automatisch die Kategorie verwendet,
    die im Plugin eingestellt wurde.

    Standard-Wert: 1

plugin.tx_ptcertification.settings.rootCategory:

    Die ID der Kategorie, welche als Haupt-Kategorie benutzt wird.

    Standard-Wert: 2

plugin.tx_ptcertification.settings.categoryTargetMapping:

    Mapping-Angaben, auf welche Seiten-ID weitergeleitet werden soll, wenn der Benutzer eine ID unterhalb der angegebenen
    Kategorie auswählt.

    Form: <Kategorie-ID>:<Seiten-ID>

    Diese Einstellung kann verwendet werden, falls für unterschiedliche Überkategorien unterschiedliche Templates genutzt werden
    sollen. In diesem Fall kann man die Templates auf unterschiedlichen Seiten konfigurieren und dann die Kategorie auf die
    Seiten mappen.

    Standard-Wert: leer

plugin.tx_ptcertification.settings.randomSelection:

    Sollen die Fragen innerhalb des Checks in zufälliger Reihenfolge erscheinen?

    Ein Wert von 1 bedeutet, dass die Fragen in zufälliger Reihenfolge ausgegeben werden.
    Ein Wert von 0 bedeutet, dass die Fragen in Reihenfolge ihrer Backend-Sortierung ausgegeben werden.

    Standard-Wert: 1

plugin.tx_ptcertification.settings.silverCertificateThreshold:

    Die Punktezahl, die ein Benutzer erreichen muss, um für ein Silberzertfikat qualifiziert zu sein

    Standard-Wert: 0

plugin.tx_ptcertification.settings.goldenCertificateThreshold:

    Die Punktezahl, die ein Benutzer erreichen muss, um für ein Goldzertfikat qualifiziert zu sein

    Standard-Wert: 0

plugin.tx_ptcertification.settings.imageFolder:

    Upload-Verzeichnis für Bilder, die innerhalb der pt_certification (Frontend und Backend) hochgeladen werden.

    Standard-Wert: uploads/tx_ptcertification/