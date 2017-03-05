# Test project to compare different php validator libraries

## Technical rules to validate

* digitField - 
    * optional, 
    * must be digit, 
    * only positive values or 0
* usernameField - 
    * optional, 
    * string, allowed characters a-z-_
    * min length 4, max length 255
* dateField - required, must be valid date with format 'YYYY-MM-DD HH:MI:SS' or 'YYYY-MM-DD HH:MI'
* booleanField - optional, must be valid boolean value, if not - false is expected
* optionField - 
    * optional
    * must match one of the following ['person', 'deal']
* optionRequiredField - required, must match one of the following ['csv', 'xls']

## Business rules to validate

1. usernameField - 
    * must be checked from Service not to exists 
2. optionField - 
    * if 'optionRequiredField' value is 'csv', this value can be only 'person'
3. digitField - 
    * required if 'optionRequiredField' = 'csv' and 'optionField' = 'person', otherwise optional
  
  
Example of valid input:
```json
{
  "digitField": 123,
  "usernameField": "awesome-username",
  "dateField": "2017-02-26 11:34:45",
  "booleanField": "true",
  "alphaNumField": "abc123",
  "optionField": "person",
  "optionRequiredField": "csv",
  "digitRelatedField": 34534
}
```