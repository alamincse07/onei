import pandas as pd
import json


merged_data = pd.read_csv('nv-data.csv')


## strip left right space
merged_data = merged_data.applymap(lambda x: x.strip() if isinstance(x, str) else x)




def generate_feed_template(df,cityName=''):
    

    if len(cityName)> 2:      
      df = df[df.city == cityName]
      df.reset_index(drop=True, inplace=True)
   

    
    col = ['Listing ID', 'Listing name', 'Final URL',
          'Image URL', 'City name', 'Description', 'Price', 'Property type', 'Listing type', 'Address']

    template = pd.DataFrame(index=df.index, columns=col)
    

    print(template.info())
    template['Listing ID'] = df['id']
    template['Listing name'] = df['title']
    template['Final URL'] = df['url']
    template['Image URL'] = df['image']
    template['City name'] = df['city']
    template['Description'] = df['short_info']
    template['Price'] = df['price'].astype(str) + " USD"
    #template['Price'] = df['price'].str.cat( " USD")
    template['Property type'] = df['property_type']
    template['Listing type'] ='sale'
    template['Address'] = df['address1']
    template.to_csv(selected_city.replace(" ", "-").lower()+'-feed.csv', index=False)
   
def get_segment(x):
      x.fillna('',inplace=True)
      address = [x['community'], x['area'], x['city'], 'BC']    
      #make unique address, remove duplicate 
      location = list(dict.fromkeys(address))
     # list(OrderedDict.fromkeys(seq))
      
      property_type =  str(x['property_type'])

      # mapping from tajmul vai
      property_type_mapping={'Apartment/Condo':'Condo','House/Single Family':'House'}

      property_type = property_type_mapping.get(property_type, property_type)
      if len(property_type) < 4: 
            print(x['property_type'])
            print(x['id'])
            property_type = ""
      price = x['price']

      if price < 500000 :
           segment =  '1'
      elif price >= 500000 and price < 1000000 :
           segment = '2'
      elif price >= 1000000 and price < 2000000:
           segment = '3'
      else:
             segment = '4'

     # return "SINGLE_PRODUCT; "+("; ").join(location)+ "; "+(property_type)+";segments "+str(segment)+""
      return "SINGLE_PRODUCT; "+("; ").join(location)+ "; "+(property_type)+";segments "+str(segment)+"".replace("; ;"," ;")


def generate_dynamic_template(df, cityName=''):

    if len(cityName) > 2:
      df = df[df.city == cityName]
      df.reset_index(drop=True, inplace=True)

   # col = ['Page URL', 'Custom label','id']
    col = ['Page URL', 'Custom label']
    template = pd.DataFrame(index=df.index, columns=col)
    template['Page URL'] = df['url']
   # template['id'] = df['id']
    template['Custom label'] = df.apply(get_segment,axis=1)

#      template[['Page URL', 'Custom label']] = read_csv.apply(
#          lambda x: pd.Series(
#              generate_label(x['display'], x['categories'], x['price_selected_currency'], x['star_rating'],
#                             x['feed'], x['property_type'], x['country_code'], x['id'], x['archived'],
#                             x['property_name_de'])), axis=1)
    template.to_csv(selected_city.replace(
        " ", "-").lower()+'-feed.csv', index=False)


selected_city = 'North Vancouver'

generate_dynamic_template(merged_data, selected_city)
