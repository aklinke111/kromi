Select
    tl_sortly_country.name As country,
    tl_sortly_subsidiary.name As subsidiary,
    tl_customer.customerNo As customerNo,
    tl_sortly_customer1.name As customer,
    tl_sortly_ktc.name As ktc,
    tl_sortly.name As model,
    tl_kr_componentsBasics.description As description,
    tl_sortly.inventoryNo As inventoryNo
From
    tl_sortly_ktc Inner Join
    tl_sortly_customer tl_sortly_customer1 On tl_sortly_customer1.sid = tl_sortly_ktc.pid Inner Join
    tl_sortly_subsidiary On tl_sortly_subsidiary.sid = tl_sortly_customer1.pid Inner Join
    tl_sortly_country On tl_sortly_country.sid = tl_sortly_subsidiary.pid Inner Join
    tl_customer On tl_sortly_customer1.sid = tl_customer.sid Inner Join
    tl_sortly On tl_sortly_ktc.sid = tl_sortly.pid Inner Join
    tl_kr_componentsBasics On tl_sortly.name = tl_kr_componentsBasics.model
Order By
    country,
    subsidiary,
    customer,
    ktc,
    inventoryNo,
    model