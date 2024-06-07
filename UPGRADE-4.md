# Upgrade to 4

- Doctrine entities have been updated to use Attributes instead of XML mappings.
- The bundle no longer includes migrations. These should be generated in your project.
- When implementing a custom validator constraint or condition, your class needs to have $validator property incl. Doctrine metadata 
