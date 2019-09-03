import pandas as pd
import json
import getopt
import sys
options, remainder = getopt.getopt(sys.argv[1:], 'c:v', ['city=', ])

city_name = ''
for opt, arg in options:
    if opt in ('-c', '--city'):
        city_name = arg

if len(city_name) < 2:
    print("No city found")
    exit()

merged_data = pd.read_csv(city_name.replace(" ", "-")+'.csv')


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
      line= "SINGLE_PRODUCT; "+("; ").join(location)+ "; "+(property_type)+";segments "+str(segment)+""

      return line.replace("; ;","; ")

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

    template.to_csv(cityName.replace(" ", "-").lower() +
                    '-feed.csv', index=False)



generate_dynamic_template(merged_data, city_name)
