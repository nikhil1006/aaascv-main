
import os

#src = '/images/gallery/community'
#src = '/images/gallery/life_skills'
src = '/images/gallery/recreation'
path = f"../site{src}"

dir_list = os.listdir(path)

ctr=0
for file in dir_list:
	ctr += 1
	url = f"{src}/{file}"
	buffer = []
	buffer.append("<div>")
	buffer.append(f"  <img class='grid-item grid-item-{ctr}' src='{url}' alt=''>")
	buffer.append("</div>")

	str = "\n".join(buffer)

	print(str)

