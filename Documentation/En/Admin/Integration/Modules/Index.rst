~~~~~~~~~~~~~~
Backend-Module
~~~~~~~~~~~~~~

Fragenbaum
~~~~~~~~~~

Der Kategorienbaum der Extension. In diesem können die Kategorien verwaltet werden (neu, editieren, löschen, verschieben)

Fragenverwaltung
~~~~~~~~~~~~~~~~

Eine Liste aller Fragen, mit Funktionen um die Fragen zu editieren, löschen und per Mail zu versenden. Mit dieser Mail
können Fehler angemerkt werden.

Das Formular, mit welchem Fragen editiert und erstellt werden, wurde erweitert. Dadurch kann folgendes erreicht werden:

    - Eine Frage kann nicht einfach gespeichert werden, wenn sie schon einmal im Check verwendet wurde. Der Fragenverwalter
      muss explizit bestätigen, ob er die Frage wirklich ändern will, oder ob eine Kopie der Frage mit der Änderung anlegen
      möchte und das Original als historisch markiert gespeichert werden soll.
    - Eine Frage kann nicht gelöscht werden, bei Klick auf Löschen wird die Frage historisch markiert.
    - Eine Frage kann nicht gespeichert werden, wenn keine Kategorie für die Frage ausgewählt wurde.

Eine Frage besteht aus folgende Datensätzen Question und Question Item. Bei Question handelt es sich um die Frage im Allgemeinen und
bei Question Items handelt es sich um die Anwortmöglichkeiten zu dieser Frage. Die Question Items werden über IRRE zur Frage hinzugefügt.
http://wiki.typo3.org/Inline_Relational_Record_Editing

Datensätze
~~~~~~~~~~

Im Seitenbaum muss zunächst an beliebiger Position ein SysFolder angelegt
werden, der als Ablage für alle relevanten Datensätze zur Verfügung steht. Die
Datensätze lassen sich in drei Kategorien unterteilen:

#. Datensätze, die durch die Extension zur Verfügung gestellt werden:

    * Question Type    

#. Datensätze, die von Redakteuren im Backend angelegt und bearbeitet werden:

    * User
    * Category
    * Question
    * Question Item

#. Datensätze, die von Frontend-Benutzern durch Ausfüllen des Fragebogens dynamisch generiert werden:

    * Questionnaire
    * Exercise
    * Answer Item

Alle Datensätze können TYPO3-konform über das Listen-Modul angelegt, modifiziert
und gelöscht werden. Von der Modifikation der Fragetypen (*Question Type*)
sollte jedoch abgesehen werden, da sie in engem Zusammenhang zu den
Funktionalitäten von pt_certificaton stehen:

.. figure:: Images/Extensions/pt_certification/Admin/Integration/Modules/ListModule.png
   :scale: 100 %

   2.2: Die verwendeten Datensatz-Typen