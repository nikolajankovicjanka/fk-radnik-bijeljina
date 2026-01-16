import type { NewsItem } from '@/types/news'

/**
 * Danas: vraća mock.
 * Sutra: zamijeni sa fetch('/api/news') ili axios, bez promjene store-a/komponenti.
 */
export async function getNews(): Promise<NewsItem[]> {
    await new Promise((r) => setTimeout(r, 200))

    return [
        {
            id: '1',
            title: 'Radnik se oprostio od Lakija',
            excerpt:
                'ФК Радник SoccerBet службено потврђује да је споразумно раскинут уговор са Николом Лакић, који ће каријеру наставити у ФК Лозница.\n' +
                '\n' +
                'Никола је у свом другом мандату у дресу ФК Радник провео три и по године, те уписао 102 наступа.\n' +
                '\n' +
                'Лакију се од срца захваљујем на свему што је урадио за Клуб, те му желимо пуно среће, успјеха и здравља у будућности.\n' +
                '\n' +
                'Једном Радник, увијек Радник.',
            image: '/news/fk-radnik-laki.jpg',
            date: '21.03.2026',
            slug: 'radnik-pobjeda-domaci-teren',
            tags: ['First team'],
        },
        {
            id: '2',
            title: 'Radnik se oprostio od kapitena',
            excerpt:
                'ФК Радник SoccerBet службено потврђује да је споразумно раскинут уговор са Крајишник Дамјаном, који ће каријеру наставити у ФК Кизилжар, који се такмичи у Премијер лиги Казахстана.\n' +
                '\n' +
                'Дамјан је за ФК Радник уписао 46 наступа, постигао 7 голова и уписао 4 асистенције.\n' +
                '\n' +
                'Нанету се од срца захваљујем на свему што је урадио за Клуб, те му желимо пуно среће, успјеха и здравља у будућности.\n' +
                '\n' +
                'Једном Радник, увијек Радник. ',
            image: '/news/fk-radnik-nane.jpg',
            date: '2026-01-06',
            slug: 'novo-pojacanje-vezni-red',
        },
        {
            id: '3',
            title: 'Prozivka FK Radnik',
            excerpt:
                'Прозивка ФК Радник SoccerBet је заказана за суботу, 10.01.2026. године, са почетком у 12 часова.\n' +
                'Уводни дио припрема ће бити одрађен у Бијељини, а у том периоду ће бити одиграна и прва припремна утакмица са екипом ФК Мачва (17.01.2026. године).\n' +
                'Екипа ће од 18.01. до 28.01. боравити у Медулину, гдје ће одиграти 4 припремне утакмице:\n' +
                '21.01.2026. - НК Гробничан (ХР),\n' +
                '24.01.2026. - ФЦ Копар (СЛО), \n',
            image: '/news/fk-radnik-prozivka.jpg',
            date: '2026-01-04',
            slug: 'omladinska-skola-rezultati',
            tags: ['Youth'],
        },
        {
            id: '4',
            title: 'Ženski tim: motivacija i rad u punom jeku',
            excerpt:
                'Ženski tim FK Radnik nastavlja sa intenzivnim treninzima i pripremama uz jasno definisane ciljeve...',
            image: '/hero/fk-radnik-bozo.jpg',
            date: '2026-01-03',
            slug: 'zenski-tim-pripreme',
            tags: ['Women'],
        },
        // dodaj još par da se “vrti”
        {
            id: '5',
            title: 'Najava utakmice: ključni meč u nastavku sezone',
            excerpt:
                'Pred nama je važna utakmica. Stručni štab radi na taktici, a navijači se očekuju u velikom broju...',
            image: '/hero/fk-radnik-hero-1.jpg',
            date: '2026-01-02',
            slug: 'najava-utakmice-kljucni-mec',
        },
        {
            id: '6',
            title: 'Iz kluba: planovi za infrastrukturu i razvoj',
            excerpt:
                'Klub radi na unapređenju uslova kroz nove projekte. Cilj je stabilan rast i bolja podrška svim selekcijama...',
            image: '/hero/fk-radnik-joma.jpg',
            date: '2026-01-01',
            slug: 'planovi-infrastruktura-razvoj',
        },
    ]
}
