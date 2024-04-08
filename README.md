# Airline events app REST API

## Additional assumptions
* Right now, the app is able to parse only HTML files in CCNX format, as the assignment required. But you can easily add more parsers for different file types (in the RosterHandler class) and formats (in a particular parser factory (e.g. in the HtmlParserFactory class)).
* A period is parsed directly from the HTML from the heading "Period: 10Jan22 to 23Jan22 (ILV - Jan de Bosman)". Why not from a select field? Because there are other filters like "Week", "Period", "Custom Period" etc.
* Assuming the current date is set to January 14, 2022, the 'next week' is defined as the period from January 17, 2022, to January 23, 2022. This definition can be easily modified in the code if needed.
* The API is protected with a sanctum, token-based mechanism. The credentials are set up in the DatabaseSeeder class and can be easily modified there.

## Available API endpoints
* All events between date x and y (GET request):

        {{BASE_URL}}airlineEvents?date_from=2022-01-10&date_to=2022-01-12

* All flights for the next week (GET request):

        {{{BASE_URL}}airlineEvents?next_week_only=1

* All Standby events for the next week (GET request):

        {{BASE_URL}}airlineEvents?next_week_only=1&activity_type=SBY

* All flights that start on the given location (GET request):
        
      {{BASE_URL}}airlineEvents?departure_airport=KRP

* Roster upload (POST request):

        {{BASE_URL}}rosters
  
        Body:
        {
          "airline": "DTR",
          "system": "ccnx",
          "file_type": "html",
          "file_content": "html file content here"
          }
