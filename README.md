##  Overview

	- Module provides the latest news feeds in JSON format, this module uses lightwait JSON API (https://jsonapi.org/format/) to publish their news articles.
	- Module can be used in any Drupal 8 websites which are using the latest Drupal version (>=8.7)
	- No extra configuration/setup needed, install the module & upload the content.


## Usage
   
   - Login as a Content Publisher
   - Under the 'Structure' menu, mousehover the 'Common List' & click on the 'News' menu item. Alternatively you can visit this URL(/admin/news-article) to land on the news content management page.
   - Click on the 'Add News' button to add the new record which will be available in API Response.
   - We have CRUD operation for all the news article. If we perform any operation here, it will dynamically update the response.
   - We can also use the search box to search the specific text in title/description.

## API Usage Example

   - Get Full List of News articles in JSON Format
     <domain-name>/jsonapi/news

   - Pegignnation: 
     <domain-name>/jsonapi/news?page[limit]=10 (Populate the First 0-10 records)
     <domain-name>/jsonapi/news?page[limit]=10&page[offset]=10 (Populate the next 10-20 records)
     <domain-name>/jsonapi/news?page[limit]=10&page[offset]=20 (Populate the next 20-30 records)

   - Sorting:
	 <domain-name>/jsonapi/news?sort=published_date (use + or blank to sort the record in ASC order)
	 <domain-name>/jsonapi/news?sort=-published_date (use - to sort the record in DESC order)

   - Show only few Fields:
     <domain-name>/jsonapi/news?fields[news--news]=description,published_date (It will populate only description & published date in response)


   - Get Specific Item:
   	 <domain-name>/jsonapi/news/{{UUID}} (UUID is unique key for news article, If we request only for specific item, we need to pass the UUID which are available in full-list API response)

   - Filter Records:
     <domain-name>/jsonapi/news?filter[title]=Sample One (filter record which has a title 'Sample One')
     <domain-name>/jsonapi/news?filter[status][value]=1 (filter records which are Published)
     <domain-name>/jsonapi/news?filter[title][condition][path]=title 
      &filter[title][condition][operator]=STARTS_WITH&filter[title][condition][value]=My (Filter record which has a title starting with 'My')

## References

  - [JSON API Specification](https://jsonapi.org/format/)
  - [Drupal Custom Module](https://www.drupal.org/docs/creating-custom-modules)
  - [Custom Entity](https://www.drupal.org/docs/8/api/entity-api/creating-a-content-entity-type-in-drupal-8)
