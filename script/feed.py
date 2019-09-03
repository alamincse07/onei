import pandas as pd
import json
from glob import glob
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
filenames = glob('../data/'+city_name.replace(" ","-")+'/properties/*.json')

def processjson(f):
    with open(f) as fo:
      data1 = json.load(fo)
    df = pd.DataFrame.from_dict(data1)
    df['fileName']= str(f)
    #print(df.info())
    return df


#processjson('1.json')

dataframes = [processjson(f) for f in filenames]

print(type(dataframes))
merged_data = pd.concat(dataframes, axis='rows')

print(merged_data.head(3))


merged_data.to_csv(city_name.replace(" ","-")+'.csv', index=False)
#merged_data.to_csv('onei-data1.csv', index=False)

col = ['Listing ID', 'Listing name', 'Final URL',
           'Image URL', 'City name', 'Description', 'Price', 'Property type', 'Listing type', 'Address']

#template = pd.DataFrame(index=merged_data.index,columns=col)


# print(template.info())
# template['Listing ID'] = merged_data['id']
# template['Listing name'] = merged_data['title']
# template['Final URL'] = merged_data['url']
# template['Image URL'] = merged_data['image']
# template['City name'] = merged_data['city']
# template['Description'] = merged_data['short_info']
# template['Price'] = merged_data['price'] + " USD"
# template['Property type'] = merged_data['property_type']
# template['Listing type'] ='sale'
# template['Address'] = merged_data['address']
# template.to_csv('feed.csv', index=False)

#template['Listing ID']= merged_data['id']
# # template['Listing ID']= merged_data['id']
# # template['Listing ID']= merged_data['id']
# # template['Listing ID']= merged_data['id']
#print(template.head(30))


