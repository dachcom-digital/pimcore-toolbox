# Upgrade Notes

#### Update from Version 1.x to Version 2.0.0
- rename globallink to dynamiclink:
```sql
UPDATE documents_elements SET type = 'dynamiclink' WHERE type = 'globallink';
```